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
