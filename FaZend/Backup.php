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
        'execs'    => array(),
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
     * @return void
     * @throws FaZend_Backup_Exception
     */
    public function execute()
    {
        logg('FaZend_Backup started, Revision: ' . FaZend_Revision::get());
        if (!$this->_options['execute']) {
            logg('No execution required, end of process');
            return;
        }
        foreach ($this->_options['policies'] as $opts) {
            $class = 'FaZend_Backup_Policy_' . ucfirst($opts['name']);
            $policy = new $class();
            $policy->setOptions($opts['options']);
        }
    }

}
