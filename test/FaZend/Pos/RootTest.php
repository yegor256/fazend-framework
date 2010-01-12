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
 * Test FaZend_Pos_Root functionality
 * 
 * 
 * @package tests
 */
class FaZend_Pos_RootTest extends AbstractTestCase 
{

    public function setUp()
    {
        parent::setUp();

        $this->_user = FaZend_User::register( 'test2', 'test2' );
        FaZend_Pos_Properties::setUserId($this->_user->__id);
    }

    public function testRootInitializationIsSingle() {
        Model_Pos_Root::$initCounter = 0;
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = FaZend_Pos_Abstract::root()->car19 = new Model_Pos_Car();
        $car->bike = new Model_Pos_Bike();
        FaZend_Pos_Abstract::cleanPosMemory();
        $bike = FaZend_Pos_Abstract::root()->car19->bike;
        $this->assertEquals(2, Model_Pos_Root::$initCounter, 'Root was initialized more than once, why?');
    }

    public function testInitializationOfSubObjectsWorksFine()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
            
        $car = FaZend_Pos_Abstract::root()->carForRoot = new Model_Pos_Car();
        $car->holder = new FaZend_StdObject();
        $car->holder->bike = FaZend_Pos_Abstract::root()->bike = new Model_Pos_Bike();

        $car[1] = new FaZend_StdObject();
        $car[1]->bike = $car->holder->bike;
        $car->ps()->save();
        
        FaZend_Pos_Abstract::cleanPosMemory();
        $root = FaZend_Pos_Abstract::root();
        $this->assertTrue($root->carForRoot instanceof Model_Pos_Car, 'Car object was not retrieved');
    }

    public function testMultipleInstantiationOfRootDoesntCreateObjects()
    {
        for ($i = 0; $i < 10; $i++) {
            FaZend_Pos_Abstract::cleanPosMemory();
            $id = FaZend_Pos_Abstract::root()->ps()->id;
            if (isset($oldId))
                $this->assertEquals($id, $oldId, 'Roots are different, why?');
            $oldId = $id;
        }
    }
    
    public function testObjectsCanBeFoundById()
    {
        $id = FaZend_Pos_Abstract::root()->ps()->id;
        FaZend_Pos_Abstract::cleanPosMemory();

        $obj = FaZend_Pos_Abstract::root()->ps()->findById($id);
        $this->assertEquals($obj->ps()->id, $id, 'IDs are different, why?');
    }

}
