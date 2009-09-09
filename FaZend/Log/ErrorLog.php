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

    const MAX_LENGTH = 20000; // maximum length of the log file, in bytes

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

            if (!$file) {
                if (APPLICATION_ENV === 'production')
                    FaZend_Exception::raise('FaZend_Log_ErrorLog_NoLogFile',
                        'error_log is not set in php.ini');
                else
                    $file = 'php://stdout';
            }

            // if we can't write to project log file, let's write to syslog
            if (!is_writable($file))
                $writer = new Zend_Log_Writer_Syslog();
            else
                $writer = new Zend_Log_Writer_Stream($file);

            self::$_instance = new FaZend_Log_ErrorLog($writer, $file);

        }

        return self::$_instance;

    }

    /**
     * Class constructor.  Create a new logger
     *
     * @param Zend_Log_Writer_Abstract|null Default writer
     * @return void
     */
    public function __construct(Zend_Log_Writer_Abstract $writer = null, $file = null) {
        parent::__construct($writer);

        $this->_file = $file;

        $this->_cutFile();
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

        $this->_cutFile();

    }

    /**
     * Cut the log file, if necessary
     *
     * @return void
     */
    protected function _cutFile() {

        if (APPLICATION_ENV !== 'production')
            return;

        // if it's still small, skip the rest
        if (filesize($this->_file) < self::MAX_LENGTH)
            return;

        // if the file is not writable - skip the process
        if (!is_writable($this->_file))
            return;

        // if not email configured - skip it
        $email = FaZend_Properties::get()->errors->email;
        if (!$email)
            return;

        // get the content of the file
        $content = @file_get_contents($this->_file);

        // email the content to the admin
        FaZend_Email::create('fazendForwardLog.tmpl')
            ->set('toEmail', $email)
            ->set('toName', 'Admin of ' . WEBSITE_URL)
            ->set('log', $content)
            ->send();

        // refresh the file
        $handle = @fopen($this->_file, 'w');
        if ($handle === false)
            return;
        if (@ftruncate($handle, 0) === false)
            return;
        @fwrite($handle, date('m/d/Y h:i') . ": file content (" . strlen($content) .
            " bytes) sent by email ({$email}) to admin\n");
        @fclose($handle);

    }

}
