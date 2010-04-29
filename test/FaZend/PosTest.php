<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_PosTest extends AbstractTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->_user = FaZend_User::register('test2', 'test2');
        FaZend_Pos_Properties::setUserId($this->_user->__id);
        FaZend_Pos_Properties::cleanPosMemory(true, true);
    }

    public function tearDown()
    {
        parent::tearDown();
        FaZend_Pos_Properties::cleanPosMemory(true, true);
    }

    public function testRootReturnsRootObject()
    {
        $root = FaZend_Pos_Properties::root();
        $this->assertTrue(
            $root instanceOf FaZend_Pos_Abstract, 
            'Root method did not return an FaZend_Pos_Abstract'
        );
    }
    
    public function testRootCanAssignPosObjects()
    {
        $root = FaZend_Pos_Properties::root();
        $root->car = new Model_Pos_Car();
    
        $this->assertTrue($root->car instanceOf Model_Pos_Car);
    }
    
    public function testRootCanRetrieveAssignedPosObjects()
    {
        $root = FaZend_Pos_Properties::root();
        $root->car = new Model_Pos_Car();
    
        $root2 = FaZend_Pos_Properties::root();
    
        $this->assertTrue($root2->car instanceOf Model_Pos_Car);
    }
    
    public function testRootCanAssignArrayItems()
    {
        $root = FaZend_Pos_Properties::root();
        $root->car = new Model_Pos_Car();
        $root->car[] = new Model_Pos_Car();
        $root->car[] = new Model_Pos_Car();
    
        $this->assertTrue(count($root->car) > 0);
    }
    
    public function testRootCanRetrieveArray()
    {
        $root = FaZend_Pos_Properties::root();
        $root->car = new Model_Pos_Car();
        $root->car[] = new Model_Pos_Car();
        $root->car[] = new Model_Pos_Car();
    
        $cars = $root->car;
    
        //TODO this is actually the best we can do.  PHP's is_array function
        // only returns true for native array
        $this->assertTrue(!empty( $cars ), 'property was not an array');
    }
    
    public function testDeletedObjectCannotBeRetrievedFromRoot()
    {
        $this->markTestIncomplete();
    }
    
    public function testGetWaitingReturnsObjectsWaitingForUser()
    {
        $this->markTestIncomplete();
    }

}
