<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * One shell task
 *
 * @package Model
 */
class FaZend_Exec extends FaZend_StdObject {

    const LOG_SUFFIX = 'log';
    const PID_SUFFIX = 'pid';
    const DATA_SUFFIX = 'data';

    const WIN_FAKE_PID = 9999;

    /**
     * Static cache of running PID's
     *
     * @var int[]
     */
    protected static $_running = array();

    /**
     * Shell command
     *
     * @var string
     */
    protected $_cmd;

    /**
     * Directory to work 
     *
     * @var string
     */
    protected $_dir;

    /**
     * Unique name of it
     *
     * @var string
     */
    protected $_name;

    /**
     * Construct it
     *
     * @param string Name of the task, unique!
     * @param string Shell cmd
     * @return void
     */
    protected function __construct($name, $cmd = null) {

        $this->_name = $name;
        $this->_cmd = $cmd;

        // load configuation data, if they exist
        $dataFile = self::_fileName(self::_uniqueId($this->_name), self::DATA_SUFFIX);
        if (file_exists($dataFile)) {
            $this->_unserialize(@file_get_contents($dataFile));
        }

    }

    /**
     * Create new task
     *
     * @param string Name of the task, unique!
     * @return FaZend_Exec
     */
    public static function create($name) {
        return new FaZend_Exec($name);
    }

    /**
     * Duration in seconds
     *
     * @return int Seconds
     */
    public function getDuration() {

        if (!$this->isRunning())
            return false;

        $pidFile = self::_fileName(self::_uniqueId($this->_name), self::PID_SUFFIX);

        if (!file_exists($pidFile))
            return false;

        return time() - @filemtime($pidFile);

    }

    /**
     * Get process id
     *
     * @return int
     */
    public function getPid() {

        if (!$this->isRunning())
            return false;

        return self::_pid(self::_uniqueId($this->_name));

    }

    /**
     * Is it still running?
     *
     * @return boolean
     */
    public function isRunning() {
        return self::_isRunning(self::_uniqueId($this->_name));
    }

    /**
     * Execute it and return log
     *
     * @return string
     */
    public function execute() {

        if ($this->isRunning())
            return self::_output(self::_uniqueId($this->_name));
                                
        // serialize and save all local variables
        if (!@file_put_contents(self::_fileName(self::_uniqueId($this->_name), self::DATA_SUFFIX), 
            $this->_serialize())) {
            FaZend_Exception::raise('FaZend_Exec_DataSaveFailure', 
                "Failed to save local data before execution");
        }

        self::_execute(self::_uniqueId($this->_name), $this->_cmd, $this->_dir);

        return self::_output(self::_uniqueId($this->_name));

    }

    /**
     * Get output
     *
     * @return string
     */
    public function output() {
        return self::_output(self::_uniqueId($this->_name));
    }

    /**
     * Stop it
     *
     * @return void
     */
    public function stop() {

        if (!$this->isRunning())
            return;

        self::_stop(self::_uniqueId($this->_name));

    }

    /**
     * Calculate unique ID of the task by its name
     *
     * @param string Name of the task, unique!
     * @return string
     */
    protected static function _uniqueId($name) {
        return md5(FaZend_Properties::get()->name . $name);
    }

    /**
     * Static file name in temp dir, with suffix
     *
     * @param string ID of the task
     * @param string|null Suffix to add
     * @return string
     */
    protected static function _fileName($id, $suffix = false) {
        return TEMP_PATH . '/' . $id . ($suffix ? '.' . $suffix : false);
    }

    /**
     * Is it running now?
     *
     * @param string ID of the task
     * @return boolean
     */
    protected static function _isRunning($id) {
        
        if (isset(self::$_running[$id]))
            return true;
        
        $pidFile = self::_fileName($id, self::PID_SUFFIX);

        // if no file - there is no process
        if (!file_exists($pidFile)) {
            self::_clear($id);
            return false;
        }

        // read process ID from file
        $pid = (int)file_get_contents($pidFile);

        // if the file is corrupted
        if ($pid === 0) {
            self::_clear($id);
            return false;
        }

        // if the process is NOT found by this ID
        if (shell_exec('ps -p ' . $pid . ' | grep ' . $pid) == '') {
            // we shall remove only PID file and work with log
            // next time we will remove log as well
            self::_clear($id, true);
        }

        return self::$_running[$id] = $pid;

    }

    /**
     * Clear files
     *
     * @param string ID of the task
     * @return boolean
     */
    protected static function _clear($id, $pidOnly = false) {

        if (!$pidOnly) {
            @unlink(self::_fileName($id, self::LOG_SUFFIX));
            @unlink(self::_fileName($id, self::DATA_SUFFIX));
        }

        @unlink(self::_fileName($id, self::PID_SUFFIX));

    }
        
    /**
     * Get output log
     *
     * @param string ID of the task
     * @return boolean|string Output of the EXEC or false
     */
    protected static function _output($id) {
        return @file_get_contents(self::_fileName($id, self::LOG_SUFFIX));
    }

    /**
     * Get process ID
     *
     * @param string ID of the task
     * @return void
     */
    protected static function _pid($id) {

        if (!self::_isRunning($id))
            return false;

        return self::$_running[$id];

    }

    /**
     * Execute the command
     *
     * @param string ID of the task
     * @param string shell command
     * @return void
     */
    protected static function _execute($id, $cmd, $dir) {
        
        // execute the command and quit, saving the PID
        // @see: http://stackoverflow.com/questions/222414/asynchronous-shell-exec-in-php
        $current = getcwd();
        chdir($dir);

        $pidFile = self::_fileName($id, self::PID_SUFFIX);

        if (self::_isWindows()) {
            $shell = 'nohup ' . $cmd . ' >> ' . 
                escapeshellarg(self::_fileName($id, self::LOG_SUFFIX)) . ' 2>&1 & echo $! > ' . 
                escapeshellarg($pidFile);
        } else {
            $shell = $cmd . ' >> ' . escapeshellarg(self::_fileName($id, self::LOG_SUFFIX)) . ' 2>&1';
            file_put_contents($pidFile, (string)self::WIN_FAKE_PID);
        }

        shell_exec($shell);
        chdir($current);

        self::$_running[$id] = (int)@file_get_contents($pidFile);

    }

    /**
     * Stop the script
     *
     * @param string ID of the task
     * @return void
     */
    protected static function _stop($id) {
        if (stristr(PHP_OS, 'win'))
            return;

        shell_exec('kill -9 ' . self::_pid($id));
    }

    /**
     * Is it windows OS?
     *
     * @return boolean
     */
    protected static function _isWindows() {
        return stristr(PHP_OS, 'win') !== false;
    }

}
