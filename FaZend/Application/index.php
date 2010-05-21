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

// prolog
require_once realpath(dirname(__FILE__) . '/prolog.php');

/**
 * @see Zend_Application
 */
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV);

/**
 * @see FaZend_Application_Bootstrap_Bootstrap
 */
require_once 'FaZend/Application/Bootstrap/Bootstrap.php';
FaZend_Application_Bootstrap_Bootstrap::prepareApplication($application);

$application->bootstrap();

// we're working from the command line?
if (defined('CLI_ENVIRONMENT') && (APPLICATION_ENV !== 'testing') && !defined('TESTING_RUNNING')) {
    $router = new FaZend_Cli_Router();
    exit($router->dispatch());
} else {
    $application->run();
}

