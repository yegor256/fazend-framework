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
     * @return float
     */
    public function getDuration() {

        if (!$this->isRunning())
            return false;

        return time() - @filemtime(self::_fileName($id, self::PID_SUFFIX));

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

        self::_execute(self::_uniqueId($this->_name), $this->_cmd, $this->_dir);

        return self::_output(self::_uniqueId($this->_name));

    }

    /**
     * Calculate unique ID of the task by its name
     *
     * @param string Name of the task, unique!
     * @return string
     */
    protected static function _uniqueId($name) {
        return md5($name);
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
        
        $pidFile = self::_fileName($id, self::PID_SUFFIX);
        $logFile = self::_fileName($id, self::LOG_SUFFIX);

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
            self::_clear($id);
            return false;
        }

        return true;

    }

    /**
     * Get output log
     *
     * @param string ID of the task
     * @return boolean
     */
    protected static function _output($id) {
        
        $logFile = self::_fileName($id, self::LOG_SUFFIX);
        $pidFile = self::_fileName($id, self::PID_SUFFIX);

        if (file_exists($logFile)) {
            return file_get_contents($logFile);
        }

        self::_clear($id);

        return 'No output yet...';

    }

    /**
     * Execute the command
     *
     * @param string ID of the task
     * @param string shell command
     * @return void
     */
    protected static function _execute($id, $cmd, $dir) {
        
        $logFile = self::_fileName($id, self::LOG_SUFFIX);
        $pidFile = self::_fileName($id, self::PID_SUFFIX);

        //if (is_writable($logFile))
        //    @file_put_contents($logFile, '> ' . preg_replace('/--password\s\".*?\"/', '--password ***', $cmd) . "\n\n");

        // execute the command and quit, saving the PID
        // @see: http://stackoverflow.com/questions/222414/asynchronous-shell-exec-in-php
        $current = getcwd();
        chdir($dir);
        shell_exec('nohup ' . 
            $cmd . ' >> ' . 
            escapeshellarg($logFile) . ' 2>&1 & echo $! > ' . 
            escapeshellarg($pidFile));
        chdir($current);

    }

    /**
     * Clear files
     *
     * @param string ID of the task
     * @return boolean
     */
    protected static function _clear($id) {

        $logFile = self::_fileName($id, self::LOG_SUFFIX);
        $pidFile = self::_fileName($id, self::PID_SUFFIX);

        @unlink($pidFile);
        @unlink($logFile);

    }
        

}
