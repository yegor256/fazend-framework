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

// you should have Zend checked out from truck
// in the directory ../../zend-trunk
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/../../zend-trunk'),
    realpath(dirname(__FILE__) . '/..'),
    get_include_path())));

// these settings are specific for the testing environment in FaZend
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/test-application'));
define('FAZEND_PATH', realpath(dirname(__FILE__) . '/../FaZend'));
define('ZEND_PATH', realpath(dirname(__FILE__) . '/../../zend-trunk/Zend'));

// we inherit from Zend Test Case
require_once 'FaZend/Test/TestCase.php';

/**
 * Parent class for all unit tests
 *
 * @package test
 */
class AbstractTestCase extends FaZend_Test_TestCase
{
    
    /**
     * Connection to database
     *
     * @var Zend_Db_Adapter
     */
    protected $_dbAdapter;
    
    /**
     * Specific setup for test environment
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->_dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
    }    

}
