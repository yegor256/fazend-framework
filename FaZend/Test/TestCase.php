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
     */
    protected static $_variables = array();

    /**
     * Setup test
     *
     * @return void
     */
    public function setUp()
    {
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
    public function fazendTestBootstrap()
    {
        // bootstrap the application
        include 'FaZend/Application/index.php';
    }    

    /**
     * Close-out the test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->resetRequest();
        $this->resetResponse();

        parent::tearDown();
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
        if (isset(self::$_variables[$name]))
            return self::$_variables[$name];
        return parent::__get($name);
    }

}
