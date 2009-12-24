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
        $this->assertTrue($root->car instanceof Model_Pos_Car, 'Car object was not retrieved');
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
