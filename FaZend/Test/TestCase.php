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

defined('APPLICATION_ENV') or define('APPLICATION_ENV', 'testing');
defined('APPLICATION_PATH') or define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));
defined('CLI_ENVIRONMENT') or define('CLI_ENVIRONMENT', true);

require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

/**
 * Test case
 *
 * @category FaZend
 */
class FaZend_Test_TestCase extends Zend_Test_PHPUnit_ControllerTestCase {

    /**
     * Setup test
     *
     * @return void
     */
    public function setUp() {
    
        // run this method before everything else
        $this->bootstrap = array($this, 'fazendTestBootstrap');

        // perform normal operations of the test case
        parent::setUp();

        // create local view, since it's a controller
        $this->view = new Zend_View();

    }
    
    /**
     * Bootstrap as usual
     *
     * @return void
     */
    public function fazendTestBootstrap() {
        
        // bootstrap the application
        include 'FaZend/Application/index.php';

    }    

    /**
     * Close-out the test
     *
     * @return void
     */
    public function tearDown() {

        $this->resetRequest();
        $this->resetResponse();

        parent::tearDown();

    }
    
}
