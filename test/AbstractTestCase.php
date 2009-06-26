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

// include path for Zend is defined in build.xml!

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

define('APPLICATION_ENV', 'testing');
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/test-application'));
define('CLI_ENVIRONMENT', true);
define('FAZEND_PATH', realpath(dirname(__FILE__) . '/../FaZend'));

class AbstractTestCase extends Zend_Test_PHPUnit_ControllerTestCase {

    /**
     * Setup test
     *
     *
     */
    public function setUp () {
    
        $this->bootstrap = array($this, 'myBootstrap');

        parent::setUp();

        $this->view = new Zend_View();

    }
    
    /**
     * Bootstrap as usual
     *
     *
     */
    public function myBootstrap () {
        include 'FaZend/Application/index.php';

        include 'SetupDB.php';
    }    

    /**
     * Close-out the test
     *
     *
     */
    public function tearDown () {
        $this->resetRequest();
        $this->resetResponse();
        parent::tearDown();
    }
    
}
