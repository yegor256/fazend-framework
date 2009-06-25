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

$startTime = microtime(true);

// Define path to application directory
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH',
		realpath(dirname(__FILE__) . '/../../../application'));

// Define path to FaZend
defined('FAZEND_PATH')
	|| define('FAZEND_PATH',
		realpath(APPLICATION_PATH . '/../library/FaZend'));

// Define application environment
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV',
		(getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : 'production'));

set_include_path(implode(PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/../library'),
	realpath(FAZEND_PATH . '/..'),
	get_include_path(),
)));

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
if (!defined('WEBSITE_URL'))
	define('WEBSITE_URL', 'http://' . preg_replace('/^www\./i', '', $_SERVER['HTTP_HOST']));

// we're working from the command line?
if (empty($_SERVER['DOCUMENT_ROOT']) && (APPLICATION_ENV != 'testing')) {
	$router = new FaZend_Cli_Router();
	echo $router->dispatch();
} else {
	if (!defined('FAZEND_DONT_RUN') && (APPLICATION_ENV != 'testing'))
		$application->run();
}

