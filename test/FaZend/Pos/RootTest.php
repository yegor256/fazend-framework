<?php

require_once 'AbstractTestCase.php';

class FaZend_Pos_RootTest extends AbstractTestCase 
{

    public function setUp()
    {
        parent::setUp();

        $this->_user = FaZend_User::register( 'test2', 'test2' );
        FaZend_Pos_Properties::setUserId($this->_user->__id);
        FaZend_Pos_Properties::cleanPosMemory(true, false);
    }

    public function tearDown()
    {
        parent::tearDown();
        FaZend_Pos_Properties::cleanPosMemory(true, false);
    }

    public function testRootInitializationIsSingle()
    {
        Model_Pos_Root::$initCounter = 0;
        FaZend_Pos_Properties::root()->car199 = $car = new Model_Pos_Car();
        FaZend_Pos_Properties::cleanPosMemory(true, false);
        FaZend_Pos_Properties::root()->car199->bike = new Model_Pos_Bike();
        FaZend_Pos_Properties::cleanPosMemory(true, false);
        $bike = FaZend_Pos_Properties::root()->car199->bike;
        $this->assertEquals(3, Model_Pos_Root::$initCounter, 
            'Root was initialized more times than expected (' . Model_Pos_Root::$initCounter . ' times), why?');
    }
    
    public function testInitializationOfSubObjectsWorksFine()
    {
        $car = FaZend_Pos_Properties::root()->car897 = new Model_Pos_Car();
        $car->holder = new FaZend_StdObject();
        $car->holder->bike = FaZend_Pos_Properties::root()->bike = new Model_Pos_Bike();
        
        $car[1] = new FaZend_StdObject();
        $car[1]->bike = $car->holder->bike;
        $car->ps()->save();
        $this->assertTrue(FaZend_Pos_Properties::root()->car897 instanceof Model_Pos_Car);
        
        FaZend_Pos_Properties::cleanPosMemory(true, false);
        $root = FaZend_Pos_Properties::root();
        
        $this->assertTrue($root->car897 instanceof Model_Pos_Car, 
            'Car object was not retrieved: ' . gettype($root->car897));
    }

    public function testMultipleInstantiationOfRootDoesntCreateObjects()
    {
        for ($i = 0; $i < 10; $i++) {
            FaZend_Pos_Properties::cleanPosMemory(true, false);
            $id = FaZend_Pos_Properties::root()->ps()->id;
            if (isset($oldId))
                $this->assertEquals($id, $oldId, 'Roots are different, why?');
            $oldId = $id;
        }
    }
    
    public function testObjectsCanBeFoundById()
    {
        $id = FaZend_Pos_Properties::root()->ps()->id;
        FaZend_Pos_Properties::cleanPosMemory(true, false);

        $obj = FaZend_Pos_Properties::root()->ps()->findById($id);
        $this->assertEquals($obj->ps()->id, $id, 'IDs are different, why?');
    }

}
