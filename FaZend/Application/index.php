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

// small simple and nice PHP functions
require_once realpath(dirname(__FILE__) . '/handler.php');

global $startTime;
$startTime = microtime(true);

// whether it's CLI?
defined('CLI_ENVIRONMENT')
    || (empty($_SERVER['DOCUMENT_ROOT']) && define('CLI_ENVIRONMENT', true));

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));

// temp files location
defined('TEMP_PATH')
    || define('TEMP_PATH', realpath(sys_get_temp_dir()));

// Define path to FaZend
defined('FAZEND_PATH')
    || define('FAZEND_PATH', realpath(APPLICATION_PATH . '/../library/FaZend'));

// Define path to Zend
defined('ZEND_PATH')
    || define('ZEND_PATH', realpath(APPLICATION_PATH . '/../library/Zend'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    realpath(FAZEND_PATH . '/..'),
    get_include_path(),
)));

// small simple and nice PHP functions
require_once 'FaZend/Application/functions.php';

// Create application, bootstrap, and run
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV);

// load application-specific options
$options = new Zend_Config_Ini(FAZEND_PATH . '/Application/application.ini', 'global', true);
$options->merge(new Zend_Config_Ini(APPLICATION_PATH . '/config/app.ini', APPLICATION_ENV));

// if the application doesn't have a bootstrap file
if (!file_exists($options->bootstrap->path)) {
    $options->bootstrap->path = FAZEND_PATH . '/Application/Bootstrap/Bootstrap.php';
    $options->bootstrap->class = 'FaZend_Application_Bootstrap_Bootstrap';
}                                             

// load system options
$application->setOptions($options->toArray());
unset($options);        

// bootstrap the application
$application->bootstrap();

// you can redefine it later, if you wish
// now we define the site URL, without the leading WWW
if (!defined('WEBSITE_URL'))
    define('WEBSITE_URL', 'http://' . preg_replace('/^www\./i', '', isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'fazend.com'));

// this flag could disable application execution
if (!defined('FAZEND_DONT_RUN')) {
    // we're working from the command line?
    if (defined('CLI_ENVIRONMENT') && (APPLICATION_ENV !== 'testing') && !defined('TESTING_RUNNING')) {
        $router = new FaZend_Cli_Router();
        exit($router->dispatch());
    } else {
        $application->run();
    }
}

