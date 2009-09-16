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
 * @package FaZend
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
     * Get class instance, Singleton pattern
     *
     * @return FaZend_Log
     */
    public function getInstance() {
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
        return $this;
    }

    /**
     * Add new writer to the stack
     *
     * @param string|Zend_Log_Writer_Abstract
     * @return $this
     */
    public function addWriter($writer) {
        if (!($writer instanceof Zend_Log_Writer_Abstract)) {
            $className = 'FaZend_Log_Writer_' . $writer;
            $writer = new $className();
        }
        $this->_loggers[] = new Zend_Log($writer);

        return $this;
    }

    /**
     * info() decorator
     *
     * @param string Message to log
     * @return void
     */
    public static function info($msg) {
        return self::getInstance()->_log('info', array($msg));
    }

    /**
     * err() decorator
     *
     * @param string Message to log
     * @return void
     */
    public static function err($msg) {
        return self::getInstance()->_log('err', array($msg));
    }

    /**
     * warn() decorator
     *
     * @param string Message to log
     * @return void
     */
    public static function warn($msg) {
        return self::getInstance()->_log('warn', array($msg));
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
     * @param array Associated array of params passed
     * @return void
     * @todo in PHP5.3 we should change it to __callStatic()
     */
    protected function _log($method, array $args) {
        foreach ($this->_loggers as $logger)
            call_user_func_array(array($logger, $method), $args);
    }

}
