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
	get_include_path(),
)));

// Create application, bootstrap, and run
require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV);

// load application-specific options
$options = new Zend_Config_Ini(FAZEND_PATH . '/Application/application.ini', 'global', true);
$options->merge(new Zend_Config_Ini(APPLICATION_PATH . '/config/app.ini', APPLICATION_ENV));

// load system options
$application->setOptions($options->toArray());

unset($options);    	

// bootstrap the application
$application->bootstrap();

if (!defined('FAZEND_DONT_RUN') && (APPLICATION_ENV != 'testing'))
	$application->run();

