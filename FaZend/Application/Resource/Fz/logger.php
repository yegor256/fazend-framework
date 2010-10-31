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
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Logging mechanism bootstraping
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_fz_logger extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Unique name of the writer
     */
    const LOG_WRITER = 'fz__syslog';

    /**
     * Name of the log writer for testing
     */
    const DEBUG_WRITER = 'fz__writer';

    /**
     * Initializes the resource
     *
     * @return void
     * @see Zend_Application_Resource_Resource::init()
     * @throws FaZend_Application_Resource_fz_logger_Exception
     */
    public function init()
    {
        $this->_bootstrap->bootstrap('fz_email');
        $this->_bootstrap->bootstrap('fz_errors');

        // remove all writers
        FaZend_Log::getInstance()->clean();

        // make it a default error handler
        FaZend_Log::getInstance()->registerErrorHandler();

        // initialize the log
        $this->_initErrorLog();

        // if testing or development - log into memory as well
        $opts = $this->getOptions();
        if (APPLICATION_ENV !== 'production' || !empty($opts['mandatory'])) {
            FaZend_Log::getInstance()->addWriter('Memory', self::DEBUG_WRITER);
        }
    }

    /**
     * Initialize error log
     *
     * @return void
     */
    protected function _initErrorLog()
    {
        // we try to get the file name from php.ini
        $stream = ini_get('error_log');

        // if it wasn't set...
        if (!$stream) {
            // and if it's a production mode - we should signal
            if (APPLICATION_ENV === 'production') {
                FaZend_Exception::raise(
                    'FaZend_Application_Resource_fz_logger_Exception',
                    '[error_log] is not set in php.ini or in app.ini'
                );
            }
            return;
        }

        // log errors in ALL environments
        FaZend_Log::getInstance()->addWriter(
            new Zend_Log_Writer_Stream($stream),
            self::LOG_WRITER // unique name of the writer
        );

        if (empty($this->_options['policy'])) {
            return;
        }

        $name = @$this->_options['policy']['name'];
        if (!is_string($name) || empty($name)) {
            FaZend_Exception::raise(
                'FaZend_Application_Resource_fz_logger_Exception',
                'Name of the log policy is not defined for fz_logger'
            );
        }
        $params = @$this->_options['policy']['params'];
        if (!empty($params) && !is_array($params)) {
            FaZend_Exception::raise(
                'FaZend_Application_Resource_fz_logger_Exception',
                'Params of the log policy are invalid for fz_logger'
            );
        }
        if (empty($params)) {
            $params = array();
        }

        FaZend_Log::getInstance()->getWriter(self::LOG_WRITER)->addFilter(
            FaZend_Log_Policy_Abstract::factory($name, $params, $stream)
        );
    }

}