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

if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', 'testing');
}
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));
}
if (!defined('CLI_ENVIRONMENT')) {
    define('CLI_ENVIRONMENT', true);
}
if (!defined('TESTING_RUNNING')) {
    define('TESTING_RUNNING', true);
}

/**
 * We include this bootstrap script only ONCE, in order
 * to avoid multiple initialization of the application, in the
 * same PHP environment
 */
include_once 'FaZend/Application/prolog.php';

/**
 * @see Zend_Test_PHPUnit_ControllerTestCase
 */
require_once 'Zend/Test/PHPUnit/ControllerTestCase.php';

/**
 * Test case
 *
 * @package Test
 */
class FaZend_Test_TestCase extends Zend_Test_PHPUnit_ControllerTestCase
{

    /**
     * Memory usage when test started
     *
     * @var integer
     */
    private $_memoryUsage;

    /**
     * Setup test
     *
     * It is very important to note that we DO NOT use default Zend 
     * {@link Zend_Test_PHPUnit_ControllerTestCase::setUp()} method. Mostly because
     * in Zend Framework front controller is completely reset every time
     * setUp() is called. We don't need this behavior, because we initialize
     * application only once, not every run of the test.
     *
     * @return void
     * @see Zend_Test_PHPUnit_ControllerTestCase::setUp()
     */
    public function setUp()
    {
        /**
         * @see Zend_Application
         */
        require_once 'Zend/Application.php';
        $this->bootstrap = new Zend_Application(APPLICATION_ENV);

        // run this method before everything else
        require_once 'FaZend/Application/Bootstrap/Bootstrap.php';
        FaZend_Application_Bootstrap_Bootstrap::prepareApplication($this->bootstrap);
        
        // run the normal setup of a test case, which will reset everything,
        // including front controoler, layout, loaders, etc. and THEN will bootstrap
        // the application provided.
        parent::setUp();

        // create local view, since it's a controller
        // $this->view = $this->bootstrap->getBootstrap()->getResource('view');
        $this->view = $this->bootstrap->getBootstrap()->getResource('view');
        
        // clean all instances of all formas
        FaZend_View_Helper_Forma::cleanInstances();

        // get total amount of memory used now, in order
        // to compare with it later, in tearDown()
        $this->_memoryUsage = memory_get_usage();
    }
    
    /**
     * Close all connections
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    
        // close connection
        $db = Zend_Db_Table::getDefaultAdapter();
        if ($db instanceof Zend_Db_Adapter_Abstract) {
            $db->closeConnection();
        }
        
        // clean cache, to save memory during testing
        FaZend_Db_ActiveTable::cleanCache();
        
        // clean it, again to save memory
        FaZend_Flyweight::clean();

        $leak = memory_get_usage() - $this->_memoryUsage;
        logg("Memory leak of %d bytes", $leak);
    }
    
}
