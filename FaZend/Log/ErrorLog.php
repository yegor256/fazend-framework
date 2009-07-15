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
 * Error log processor
 *
 * @package FaZend_Log
 */
class FaZend_Log_ErrorLog extends Zend_Log {

    const MAX_LENGTH = 20000; // maximum length of the log file

    /**
     * Instance of the class, singleton pattern
     *
     * @var FaZend_Log_ErrorLog
     */
    protected static $_instance;

    /**
     * Error_log file name
     *
     * @var string
     */
    protected $_file;

    /**
     * Get the instance of the log
     *
     * @return value|false
     */
    public static function getInstance() {

        if (!isset(self::$_instance)) {

            $file = ini_get('error_log');

            if (!$file)
                FaZend_Exception::raise('FaZend_Log_ErrorLog_NoLogFile', 
                    'error_log is not set in php.ini');

            self::$_instance = new FaZend_Log_ErrorLog(new Zend_Log_Writer_Stream($file));
            self::$_instance->_file = $file;

        }

        return self::$_instance;

    }

    /**
     * Log a message at a priority
     *
     * @param  string   $message   Message to log
     * @param  integer  $priority  Priority of message
     * @return void
     * @throws Zend_Log_Exception
     */
    public function log($message, $priority) {

        parent::log($message, $priority);

        if (APPLICATION_ENV !== 'production')
            return;

        // if it's still small, skip the rest
        if (filesize($this->_file) < self::MAX_LENGTH)
            return;
        
        if (FaZend_Properties::get()->errors->email) {
            // email the content to the admin
            FaZend_Email::create('fazendForwardLog.tmpl')
                ->set('toEmail', FaZend_Properties::get()->errors->email)
                ->set('toName', 'Admin of ' . WEBSITE_URL)
                ->set('log', file_get_contents($this->_file))
                ->send();
        }

        // refresh the file
        file_put_contents($this->_file, 'file content sent by email to admin');

    }

}
