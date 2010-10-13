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
 * Logger, for testing and production environments
 *
 * Implemented in some sort of Observer pattern, where you register
 * as many log writers as you wish and all of them will receive messages
 * sent to FaZend_Log static methods. For example:
 *
 * <code>
 * FaZend_Log::addWriter(new FaZend_Log_Writer_Debug());
 * FaZend_Log::addWriter('ErrorLog');
 *
 * FaZend_Log::info('File was created');
 * logg('File was created'); // it's the same, but shorter
 * </code>
 *
 * In the example above the message will be sent to both writers.
 *
 * @package Log
 * @see logg()
 */
class FaZend_Log
{

    /**
     * Class instance
     *
     * @var FaZend_Log
     */
    protected static $_instance = null;

    /**
     * Collection of loggers
     *
     * @var Zend_Log[]
     */
    protected $_loggers = array();

    /**
     * Collection of writers
     *
     * One writer per one logger.
     *
     * @var Zend_Log_Writer_Abstract[]
     */
    protected $_writers = array();

    /**
     * Get class instance, Singleton pattern
     *
     * @return FaZend_Log
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new FaZend_Log();
        }
        return self::$_instance;
    }

    /**
     * Protected constructor
     *
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * Clear the queue of writers
     *
     * @return $this
     */
    public function clean()
    {
        $this->_loggers = array();
        $this->_writers = array();
        return $this;
    }

    /**
     * Add new writer to the stack
     *
     * @param string|Zend_Log_Writer_Abstract
     * @param string Name of the writer
     * @return $this
     * @throws FaZend_Log_InvalidWriterName
     */
    public function addWriter($writer, $name = null)
    {
        if (!($writer instanceof Zend_Log_Writer_Abstract)) {
            if (!is_string($writer)) {
                FaZend_Exception::raise(
                    'FaZend_Log_InvalidWriterName',
                    'Writer can be an instance of Zend_Log_Writer_Abstract or a string'
                );
            }

            $className = 'FaZend_Log_Writer_' . $writer;
            $writer = new $className();
        }

        // create a unique name
        if (is_null($name)) {
            $name = get_class($writer) . '1';
            foreach (array_keys($this->_loggers) as $id) {
                $matches = array();
                if (preg_match('/^(' . preg_quote(get_class($writer)) . ')(\d+)$/', $id, $matches)) {
                    $name = $matches[1] . ((int)$matches[2] + 1);
                }
            }
        }

        $this->_loggers[$name] = new Zend_Log($writer);
        $this->_writers[$name] = $writer;

        return $this;
    }

    /**
     * Remove writer from the stack
     *
     * @param string Name of the writer
     * @return $this
     * @throws FaZend_Log_WriterNotFound
     */
    public function removeWriter($name)
    {
        if (!isset($this->_loggers[$name])) {
            FaZend_Exception::raise(
                'FaZend_Log_WriterNotFound', 
                "Writer '{$name}' was not found in stack"
            );
        }

        unset($this->_loggers[$name]);
        unset($this->_writers[$name]);

        return $this;
    }

    /**
     * Get writer from the stack
     *
     * @param string Name of the writer
     * @return Zend_Log_Writer_Abstract
     * @throws FaZend_Log_WriterNotFound
     */
    public function getWriter($name)
    {
        if (!$this->hasWriter($name)) {
            FaZend_Exception::raise(
                'FaZend_Log_WriterNotFound', 
                "Writer '{$name}' was not found in stack"
            );
        }

        return $this->_writers[$name];
    }

    /**
     * Do we have the writer with this name?
     *
     * @param string Name of the writer
     * @return boolean
     */
    public function hasWriter($name)
    {
        return isset($this->_loggers[$name]);
    }

    /**
     * Get writer from the stack, and kill it there
     *
     * @param string Name of the writer
     * @return Zend_Log_Writer_Abstract
     */
    public function getWriterAndRemove($name)
    {
        $writer = $this->getWriter($name);
        $this->removeWriter($name);
        return $writer;
    }
    
    /**
     * info() decorator
     *
     * @param string Message to log
     * @return void
     */
    public static function info($msg)
    {
        // pass it to sprintf
        if (func_num_args() > 1) {
            $args = func_get_args();
            $msg = call_user_func_array(
                'sprintf', 
                array_merge(
                    array($msg), 
                    array_slice($args, 1)
                )
            );
        }
        return self::getInstance()->_log('info', $msg);
    }

    /**
     * err() decorator
     *
     * @param string Message to log
     * @return void
     */
    public static function err($msg)
    {
        // pass it to sprintf
        if (func_num_args() > 1) {
            $args = func_get_args();
            $msg = call_user_func_array(
                'sprintf', 
                array_merge(
                    array($msg), 
                    array_slice($args, 1)
                )
            );
        }
        return self::getInstance()->_log('err', $msg);
    }

    /**
     * warn() decorator
     *
     * @param string Message to log
     * @return void
     */
    public static function warn($msg)
    {
        // pass it to sprintf
        if (func_num_args() > 1) {
            $args = func_get_args();
            $msg = call_user_func_array(
                'sprintf', 
                array_merge(
                    array($msg), 
                    array_slice($args, 1)
                )
            );
        }
        return self::getInstance()->_log('warn', $msg);
    }

    /**
     * Log calls processor
     *
     * Use this class whereever you need in your code. The messages sent to log
     * will be stored in your system log file or in stdout, if you're running tests:
     *
     * <code>
     * FaZend_Log::err('Some error happened');
     * FaZend_Log::info('New user registered');
     * </code>
     *
     * @param string Name of the method called
     * @param string Message to log
     * @return void
     * @todo in PHP5.3 we should change it to __callStatic()
     */
    protected function _log($method, $message)
    {
        foreach ($this->_loggers as $logger) {
            call_user_func_array(array($logger, $method), array((string)$message));
        }
    }

}
