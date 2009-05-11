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

require_once 'PHPUnit/Framework/TestCase.php';

// bootstrap the application
define('APPLICATION_ENV', 'testing');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/test-application'));
define('FAZEND_PATH', realpath(dirname(__FILE__) . '/../FaZend'));
include 'FaZend/Application/index.php';

class AbstractTestCase extends PHPUnit_Framework_TestCase {
	
}
