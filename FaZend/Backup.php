<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Backup database and files and send them to amazon or to FTP server.
 *
 * It is executed automatically from CLI script (FzBackup). You should configure
 * its behavior by means of application/app.ini file. See an example
 * in test-application.
 *
 * @package Backup
 * @see app/cli/FzBackup.php
 */
class FaZend_Backup
{

    /**
     * Full list of all options.
     *
     * @var array
     */
    protected $_options = array(
        'execute'  => false, // shall we execute it at all?
        'period'   => 6, // hours
        'policies' => array(), // list of policies to configure
    );
    
    /**
     * Instance of the class, in singleton pattern.
     *
     * @var FaZend_Backup
     */
    protected static $_instance = null;

    /**
     * Get instnace of the class
     *
     * @return FaZend_Backup
     */
    public static function getInstance() 
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Construct the class.
     *
     * @return void
     */
    private function __construct()
    {
    }
    
    /**
     * Get one or all options.
     *
     * @param string Name of the option or NULL if ALL opts are required
     * @return mixed
     */
    public function getOption($name = null) 
    {
        if (is_null($name)) {
            return $this->_options;
        }
        if (!array_key_exists($name, $this->_options)) {
            FaZend_Exception::raise(
                'FaZend_Backup_Exception',
                "Option '{$name}' doesn't exist in " . get_class($this)
            );
        }
        return $this->_options[$name];
    }

    /**
     * Set options before execution.
     *
     * @param array List of options, associative array
     * @return void
     */
    public final function setOptions(array $options)
    {
        foreach ($options as $k=>$v) {
            if (!array_key_exists($k, $this->_options)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Exception',
                    "Invalid option '{$k}' for " . get_class($this)
                );
            }
            $this->_options[$k] = $v;
        }
    }

    /**
     * Execute backup process
     *
     * @param string Absolute file name of the log
     * @return void
     * @throws FaZend_Backup_Exception
     */
    public function execute($log)
    {
        logg(
            'FaZend_Backup started, revision: %s, process ID: %d',
            FaZend_Revision::get(),
            getmypid()
        );
        if (!$this->_options['execute']) {
            logg('No execution required, end of process');
            return;
        }
        
        // create temp directory
        $dir = tempnam(TEMP_PATH, 'FaZend_Backup-' . FaZend_Revision::getName() . '-');
        if (@unlink($dir) === false) {
            FaZend_Exception::raise(
                'FaZend_Backup_Exception',
                "Failed to unlink('{$dir}')"
            );
        }
        if (@mkdir($dir) === false) {
            FaZend_Exception::raise(
                'FaZend_Backup_Exception',
                "Failed to mkdir('{$dir}')"
            );
        }
        logg("Temporary directory created: '%s'", $dir);

        // configure them all
        $policies = array();
        foreach ($this->_options['policies'] as $opts) {
            $class = 'FaZend_Backup_Policy_' . ucfirst($opts['name']);
            $policy = new $class($log);
            if (array_key_exists('options', $opts)) {
                $policy->setOptions($opts['options']);
            }
            $policy->setDir($dir);
            $policies[] = $policy;
        }

        // execute them one by one
        foreach ($policies as $p) {
            logg(
                '[%s]',
                get_class($p)
            );
            $p->forward();
        }
        
        // delete the temp directory
        $this->_delDir($dir);
        
        logg(
            "Temporary directory removed: '%s'", 
            pathinfo($dir, PATHINFO_BASENAME)
        );

        logg(
            'FaZend_Backup finished, next run expected in %d hours', 
            $this->_options['period']
        );
    }
    
    /**
     * Remove directory recursively.
     *
     * @param string Absolute path of the directory
     * @return void
     * @throws FaZend_Backup_Exception
     */
    protected function _delDir($dir) 
    {
        foreach (new DirectoryIterator($dir) as $file) { 
            if ($file->isDot()) {
                continue;
            }
            if ($file->isDir()) {
                $this->_delDir($file->getPathname());
            } else {
                if (@unlink($file->getPathname()) === false) {
                    FaZend_Exception::raise(
                        'FaZend_Backup_Exception',
                        "Failed to unlink('{$file}')"
                    );
                }
            }
        } 
        if (@rmdir($dir) === false) {
            FaZend_Exception::raise(
                'FaZend_Backup_Exception',
                "Failed to rmdir('{$dir}')"
            );
        }
    }

}
