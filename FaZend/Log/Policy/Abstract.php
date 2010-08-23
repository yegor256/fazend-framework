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
 * Abstract log policy
 *
 * @package Log
 * @see logg()
 */
abstract class FaZend_Log_Policy_Abstract implements Zend_Log_Filter_Interface
{

    /**
     * Flag to avoid endless cycles
     *
     * @var boolean
     * @see accept()
     */
    protected $_running = false;

    /**
     * Associative array of options
     *
     * @var array
     * @see __construct
     */
    protected $_options = array();
    
    /**
     * Absolute name of the log file
     *
     * @var string
     * @see setFile()
     */
    protected $_file;

    /**
     * Returns TRUE to accept the message, FALSE to block it.
     *
     * @param  array event data
     * @return boolean accepted?
     * @see Zend_Log_Filter_Interface
     */
    public function accept($event)
    {
        if (!$this->_running) {
            $this->_running = true;
            $this->_run();
            $this->_running = false;
        }
        return true;
    }
    
    /**
     * Construct the class
     *
     * @param array List of options
     * @param string Absolute name of the file
     * @return void
     * @throws FaZend_Log_Policy_Abstract_InvalidOptionException
     */
    private function __construct(array $options, $file)
    {
        foreach ($options as $option=>$value) {
            if (!array_key_exists($option, $this->_options)) {
                FaZend_Exception::raise(
                    'FaZend_Log_Policy_Abstract_InvalidOptionException',
                    "Option '{$option}' is not accepted in " . get_class($this)
                );
            }
            $this->_options[$option] = $value;
        }
        // sanity check
        if (!is_string($file)) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Abstract_InvalidOptionException',
                "Stream specification is not valid, string expected"
            );
        }
        $isPhpStream = (substr($file, 0, 6) == 'php://');
        // if it's not a regular file - skip the process
        if (!@is_file($file) && !$isPhpStream) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Abstract_Exception',
                "Stream provided ({$file}) is not a regular file"
            );
        }
        // if the file is not writable - skip the process
        if (!@is_writable($file) && !$isPhpStream) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Abstract_Exception',
                "Stream '{$file}' is not writable"
            );
        }

        $this->_file = $file;
    }

    /**
     * Create new policy
     *
     * @param string Name of the class to use
     * @param array List of params
     * @param string Absolute name of the file
     * @return FaZend_Log_Policy_Abstract
     * @see FaZend_Application_Resource_fz_logger
     */
    public static function factory($name, array $options, $file) 
    {
        $className = 'FaZend_Log_Policy_' . ucfirst($name);
        return new $className($options, $file);
    }
    
    /**
     * Run the policy
     *
     * @return void
     */
    abstract protected function _run();
    
    /**
     * Truncate the original log file
     *
     * @param string Absolute file name
     * @return void
     */
    protected function _truncate($file) 
    {
        $handle = @fopen($file, 'w');
        if ($handle === false) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Abstract_Exception',
                "Failed to fopen({$file})"
            );
        }
        if (@ftruncate($handle, 0) === false) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Abstract_Exception',
                "Failed to ftruncate({$file})"
            );
        }
        @fclose($handle);
    }

}
