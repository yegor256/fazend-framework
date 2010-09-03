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
 * Re-defining of PHP error handler
 */
function fz__ErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (in_array($errno, array(E_WARNING)) && error_reporting() == 0) {
        return;
    }
    switch ($errno) {
        case E_WARNING:
            $err = 'WARNING';
            break;
        case E_USER_ERROR:
            $err = 'USER ERROR';
            break;
        case E_USER_WARNING:
            $err = 'USER WARNING';
            break;
        case E_USER_NOTICE:
            $err = 'USER NOTICE';
            break;
        case E_NOTICE:
            $err = 'NOTICE';
            break;
        default:
            $err = 'OTHER';
            break;
    }
    echo sprintf(
        "[%s] %s, file: %s(%d)\n",
        $err, 
        $errstr,
        $errfile,
        $errline
    );
}
set_error_handler('fz__ErrorHandler');

/**
 * Global variable in order to calculate total page building time
 * @see FaZend_View_Helper_PageLoadTime::pageLoadTime()
 */
global $startTime;
$startTime = microtime(true);

/**
 * @see FaZend_Application_Resource_fz_session::init()
 * @see FaZend_Application_Resource_fz_front::init()
 * @see FaZend_Test_TestCase
 */
if (!defined('CLI_ENVIRONMENT')) {
    if (empty($_SERVER['DOCUMENT_ROOT']) || defined('STDIN')) {
        define('CLI_ENVIRONMENT', true);
    }
}

// Define path to application directory
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));
}

// Define path to FaZend
if (!defined('FAZEND_PATH')) {
    define('FAZEND_PATH', realpath(APPLICATION_PATH . '/../library/FaZend'));
}

// Define path to FaZend application inside framework
if (!defined('FAZEND_APP_PATH')) {
    define('FAZEND_APP_PATH', realpath(FAZEND_PATH . '/app'));
}

// Define path to Zend
if (!defined('ZEND_PATH')) {
    define('ZEND_PATH', realpath(APPLICATION_PATH . '/../library/Zend'));
}

// Define application environment
if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));
}

set_include_path(
    implode(
        PATH_SEPARATOR, 
        array_unique(
            array_merge(
                explode(
                    PATH_SEPARATOR,
                    get_include_path()
                ),
                array(
                    realpath(APPLICATION_PATH),
                    realpath(APPLICATION_PATH . '/../library'),
                    realpath(FAZEND_PATH . '/..'),
                )
            )
        )
    )
);

/**
 * small simple and nice PHP functions
 */
require_once 'FaZend/Application/functions.php';

// temp files location
if (!defined('TEMP_PATH')) {
    define('TEMP_PATH', realpath(sys_get_temp_dir()));
}

/**
 * you can redefine it later, if you wish
 * now we define the site URL, without the leading WWW
 */
if (!defined('WEBSITE_URL')) {
    define(
        'WEBSITE_URL', 
        'http://' . preg_replace(
            '/^www\./i', 
            '', 
            isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost'
        )
    );
}  
