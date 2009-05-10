<?php
/**
 *
 * Copyright (c) 2009, Caybo.ru
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of Caybo.ru. located at
 * www.caybo.ru. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@caybo.ru
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) Caybo.ru, 2009
 * @version $Id$
 *
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

class DefaultTestCase extends Zend_Test_PHPUnit_ControllerTestCase {

	public function setUp() {

		parent::setUp();

//		$this->frontController->setControllerDirectory(APPLICATION_PATH . '/controllers');

//		$this->fail ('DIRECTORY: '.var_export ($this->frontController->getControllerDirectory()));
	}

//	public $bootstrap = '../src/application/bootstrap.php';

}
