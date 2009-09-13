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
 * @package FaZend
 */
class FaZend_Log {

    /**
     * Instance of Zend_Log
     *
     * @var Zend_Log
     */
    protected static $_logger;

    /**
     * info() decorator
     *
     */
    public static function info($msg) {
        return self::_callStatic('info', array($msg));
    }

    /**
     * err() decorator
     *
     */
    public static function err($msg) {
        return self::_callStatic('err', array($msg));
    }

    /**
     * warn() decorator
     *
     */
    public static function warn($msg) {
        return self::_callStatic('warn', array($msg));
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
    protected static function _callStatic($method, array $args) {

        if (!isset(self::$_logger)) {
            if (APPLICATION_ENV === 'production')
                self::$_logger = FaZend_Log_ErrorLog::getInstance();
            else
                self::$_logger = new Zend_Log(new FaZend_Log_Writer_Debug());
        }

        return call_user_func_array(array(self::$_logger, $method), $args);
    }

}
