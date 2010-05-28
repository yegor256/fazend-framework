<?php
/**
 * @version $Id$
 */

// these settings are specific for the testing environment in FaZend
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/test-application'));
define('FAZEND_PATH', realpath(dirname(__FILE__) . '/../FaZend'));

// you should have Zend checked out from truck
// in the directory ../../zend-trunk
define('ZEND_PATH', realpath(dirname(__FILE__) . '/../../zend-trunk/Zend'));

set_include_path(
    implode(
        PATH_SEPARATOR, 
        array_unique(
            array_merge(
                array(
                    realpath(ZEND_PATH . '/..'),
                    realpath(dirname(__FILE__) . '/..'),
                ),
                explode(
                    PATH_SEPARATOR,
                    get_include_path()
                )
            )
        )
    )
);

/**
 * @see FaZend_Test_TestCase
 */
require_once 'FaZend/Test/TestCase.php';

class AbstractTestCase extends FaZend_Test_TestCase
{
    
    /**
     * @var Zend_Db_Adapter
     */
    protected $_dbAdapter;
    
    public function setUp()
    {
        parent::setUp();
        $this->_dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
    }    

    public function tearDown()
    {
        parent::tearDown();
    }    

}
