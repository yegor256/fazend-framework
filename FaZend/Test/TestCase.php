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

// we start execution from php/test directory
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../src/application/'));
set_include_path(
	// main application
	APPLICATION_PATH . PATH_SEPARATOR .

	// Zend library
	APPLICATION_PATH . '/../library' . PATH_SEPARATOR . 

	// default path
	get_include_path());

define('APPLICATION_ENV', 'testing');

require_once 'Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/config/app.ini');
$application->bootstrap()
            ->run();

class FaZend_Test_TestCase extends Zend_Test_PHPUnit_ControllerTestCase {

}
