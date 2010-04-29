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

defined('APPLICATION_ENV') or define('APPLICATION_ENV', 'testing');
defined('FAZEND_DONT_RUN') or define('FAZEND_DONT_RUN', true);
defined('APPLICATION_PATH') or define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../../application'));
defined('CLI_ENVIRONMENT') or define('CLI_ENVIRONMENT', true);
defined('TESTING_RUNNING') or define('TESTING_RUNNING', true);

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
     * List of variables
     *
     * @var array
     * @see __set()
     * @see __get()
     */
    protected static $_variables = array();

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
        // bootstrap the application
        // we include this bootstrap script only ONCE, in order
        // to avoid multiple initialization of the application, in the
        // same PHP environment
        include_once 'FaZend/Application/index.php';

        // run this method before everything else
        require_once 'FaZend/Application/Bootstrap/Bootstrap.php';
        $this->bootstrap = FaZend_Application_Bootstrap_Bootstrap::prepareApplication();
        
        // run the normal setup of a test case, which will reset everything,
        // including front controoler, layout, loaders, etc. and THEN will bootstrap
        // the application provided.
        parent::setUp();

        // create local view, since it's a controller
        $this->view = $this->bootstrap->getBootstrap()->getResource('view');
        
        // clean all instances of all formas
        FaZend_View_Helper_Forma::cleanInstances();
    }
    
    /**
     * Save local variables
     *
     * @param string Name of the variable
     * @param string Value of the variable
     * @return void
     */
    public function __set($name, $value)
    {
        self::$_variables[$name] = $value;
    }

    /**
     * Load local variables
     *
     * @param string Name of the variable
     * @return string
     */
    public function __get($name)
    {
        if (isset(self::$_variables[$name])) {
            return self::$_variables[$name];
        }
        return parent::__get($name);
    }

}
