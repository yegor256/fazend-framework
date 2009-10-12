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
 * </code>
 *
 * In the example above the message will be sent to both writers.
 *
 * @package Log
 */
class FaZend_Log {

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
    public static function getInstance() {
        if (is_null(self::$_instance))
            self::$_instance = new FaZend_Log();
        return self::$_instance;
    }

    /**
     * Protected constructor
     *
     * @return void
     */
    protected function __construct() {
    }

    /**
     * Clear the queue of writers
     *
     * @return $this
     */
    public function clean() {
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
     */
    public function addWriter($writer, $name = null) {
        if (!($writer instanceof Zend_Log_Writer_Abstract)) {

            if (!is_string($writer))
                FaZend_Exception::raise('FaZend_Log_InvalidWriterName',
                    "Writer can be an instance of Zend_Log_Writer_Abstract or a string");

            $className = 'FaZend_Log_Writer_' . $writer;
            $writer = new $className();
        }

        // create a unique name
        if (is_null($name)) {
            $name = get_class($writer) . '1';
            foreach ($this->_loggers as $id=>$logger) {
                $matches = array();
                if (preg_match('/^(' . preg_quote(get_class($writer)) . ')(\d+)$/', $id, $matches))
                    $name = $matches[1] . ((int)$matches[2] + 1);
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
    public function removeWriter($name) {
        if (!isset($this->_loggers[$name]))
            FaZend_Exception::raise('FaZend_Log_WriterNotFound', "Writer '{$name}' was not found in stack");

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
    public function getWriter($name) {
        if (!isset($this->_loggers[$name]))
            FaZend_Exception::raise('FaZend_Log_WriterNotFound', "Writer '{$name}' was not found in stack");

        return $this->_writers[$name];
    }

    /**
     * Get writer from the stack, and kill it there
     *
     * @param string Name of the writer
     * @return Zend_Log_Writer_Abstract
     */
    public function getWriterAndRemove($name) {
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
    public static function info($msg) {
        return self::getInstance()->_log('info', $msg);
    }

    /**
     * err() decorator
     *
     * @param string Message to log
     * @return void
     */
    public static function err($msg) {
        return self::getInstance()->_log('err', $msg);
    }

    /**
     * warn() decorator
     *
     * @param string Message to log
     * @return void
     */
    public static function warn($msg) {
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
    protected function _log($method, $message) {
        foreach ($this->_loggers as $logger)
            call_user_func_array(array($logger, $method), array((string)$message));
    }

}
