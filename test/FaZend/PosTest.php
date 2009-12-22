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

require_once 'AbstractTestCase.php';

/**
 * TODO: short description.
 * 
 * TODO: long description.
 * 
 */
class FaZend_PosTest extends AbstractTestCase 
{

    public function setUp()
    {
        parent::setUp();

        $this->_user = FaZend_User::register('test2', 'test2');
        $this->_user->logIn();
    }

    public function testRootReturnsRootObject()
    {
        $root = FaZend_Pos_Abstract::root();
        $this->assertTrue($root instanceOf FaZend_Pos_Abstract, 
            'Root method did not return an FaZend_Pos_Abstract');
    }
    
    public function testRootCanAssignPosObjects()
    {
        $root = FaZend_Pos_Abstract::root();
        $root->car = new Model_Pos_Car();
    
        $this->assertTrue($root->car instanceOf Model_Pos_Car);
    }
    
    public function testRootCanRetrieveAssignedPosObjects()
    {
        $root = FaZend_Pos_Abstract::root();
        $root->car = new Model_Pos_Car();
    
        $root2 = FaZend_Pos_Abstract::root();
    
        $this->assertTrue($root2->car instanceOf Model_Pos_Car);
    }
    
    public function testRootCanAssignArrayItems()
    {
        $root = FaZend_Pos_Abstract::root();
        $root->car = new Model_Pos_Car();
        $root->car[] = new Model_Pos_Car();
        $root->car[] = new Model_Pos_Car();
    
        $this->assertTrue(count($root->car) > 0);
    }
    
    public function testRootCanRetrieveArray()
    {
        $root = FaZend_Pos_Abstract::root();
        $root->car = new Model_Pos_Car();
        $root->car[] = new Model_Pos_Car();
        $root->car[] = new Model_Pos_Car();
    
        $cars = $root->car;
    
        //TODO this is actually the best we can do.  PHP's is_array function
        // only returns true for native array
        $this->assertTrue( !empty( $cars ), 'property was not an array' );
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
