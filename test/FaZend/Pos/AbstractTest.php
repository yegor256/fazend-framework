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
class FaZend_Pos_AbstractTest extends AbstractTestCase 
{

    public function setUp()
    {
        parent::setUp();

        FaZend_Pos_Abstract::cleanPosMemory();
        $this->_user = FaZend_User::register( 'test2', 'test2' );
        FaZend_Pos_Properties::setUserId($this->_user->__id);
    }

    public function testCanAssignValuesToProperties()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $trims = array( 'Coupe', 'Sedan' );
        
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;
        
        $car->make  = 'BMW';
        $car->model = '330xi';
        $car->year  = 2009;
        $car->active = true;
        $car->trims = $trims;

        $this->assertEquals( 'BMW', $car->make, 'Could not retreive "make" property value' );
        $this->assertEquals( '330xi', $car->model, 'Could not retreive "model" property value' );
        $this->assertEquals( 2009, $car->year, 'Could not retreive "year" property value' );
        #$this->assertEquals( $trims, $car->trims, 'Could not retreive "trims" property value' );
    }

    public function testPropertiesAreUniquePerObject()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;

        $car->make  = 'BMW';
        $car->year  = 2009;
        
        $car2 = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car2 = $car2;
        
        $car2->make  = 'Honda';
        $car2->year  = 2003;

        $this->assertNotEquals( 
            $car->make, 
            $car2->make, 
            'Different objects have same value. Why?'
        );

        $this->assertNotEquals( 
            $car->year, 
            $car2->year, 
            'Different objects have same value. Why?'
        );

    }

    public function testPropertyValueCanBeNull()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;
        
        $car->make = null;
        $this->assertNull( $car->make, 'Property value was not null!' );
    }

    public function testIssetWorksWithProperties()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;

        $car->make = null;
        $this->assertFalse( isset( $car->model ), 'Unasigned property reported as set!' );
        $this->assertFalse( isset( $car->make ), 'Nulled property reported as set!' );
        
        $car->bike = new Model_Pos_Car();
        FaZend_Pos_Abstract::cleanPosMemory();
        $this->assertTrue(isset(FaZend_Pos_Abstract::root()->car->bike), 'Property lost?');
    }

    public function testUnsetWorksWithProperties()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;

        $car->make = 'Nissan';
        unset( $car->make );
        $this->assertFalse( isset( $car->make ), 'Property value was still set!');
    }

    /**
     * @expectedException FaZend_Pos_Properties_PropertyMissed
     */
    public function testUnassignedPropertyReturnsNull()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;

        $something = $car->something;
    }

    public function testSaveCreatesNewVersion()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;

        $car->make  = 'Nissan';
        $car->model = 'Maxima';
        $car->ps()->save();
        $car->year = 2009;
        $car->ps()->save();

        $result = $this->_dbAdapter->fetchAll("SELECT * FROM fzSnapshot");

        // 4 snapshots: 2 for root and two for the object
        $this->assertEquals( 4, count($result),
            'FaZend_Pos_Abstrast::save() did not create unique versions' );
    }

    public function testSerializeObjectSavesSnapshotOnSerialize()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;

        $car->make  = 'Nissan';
        $car->model = 'Maxima';
        $car->active = false;
        serialize( $car );

        $result = $this->_dbAdapter->fetchAll("SELECT * FROM fzSnapshot");
        
        $this->assertTrue(count($result) > 0, 'Serialize did not save object');
    }

    public function testSerializedObjectReceivesUpdatesOnUnserialize()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;

        $car->make  = 'Nissan';
        $car->model = 'Maxima';
        $car->active = false;

        $serialized = serialize( $car );

        $car->active = true;
        $car->ps()->save();

        $car2 = unserialize( $serialized );
        $this->assertTrue( $car2->active, 'Unserialized object did not recieve updated property values' );
    }

    public function testThereIsOnlyOneRoot()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        FaZend_Pos_Abstract::root()->car = new Model_Pos_Car();

        for ($i=0; $i<10; $i++) {
            FaZend_Pos_Abstract::cleanPosMemory();
            $car = FaZend_Pos_Abstract::root()->car;
        }
        $result = $this->_dbAdapter->fetchAll("SELECT * FROM fzSnapshot");
        $this->assertEquals(2, count($result), 'Root object produces many snapshots when created');
    }
    
    public function testObjectCanHaveSubObjects() {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;
        
        $car->bike = new Model_Pos_Bike();
        $car->bike->owners = array('Jim', 'Nick');
        
        $car->bike->price = '1670 USD';
        $car->bike->ps()->save(true);
        FaZend_Pos_Abstract::cleanPosMemory();

        $bike = FaZend_Pos_Abstract::root()->car->bike;
        $this->assertEquals($bike->price, '1670 USD', 'Object is lost, why?');
        $this->assertTrue(count($bike->owners) == 2, 'Array inside the object is lost, why?');
    }

    public function testObjectCanBeVeryDeep() {
        FaZend_Pos_Abstract::cleanPosMemory();
        
        $obj = FaZend_Pos_Abstract::root()->obj = new Model_Pos_Car();

        for ($i = 0; $i<25; $i++) {
            $obj->obj = new Model_Pos_Bike();
            $obj = $obj->obj;
        }
        
        FaZend_Pos_Abstract::root()->ps()->save(true);
    }

    public function testObjectsAreLoadedFromDatabase() {
        FaZend_Pos_Abstract::cleanPosMemory();

        $queries = array(
             // clear everything beforehand
            'DELETE FROM fzObject',
            'DELETE FROM fzSnapshot',
            'DELETE FROM fzPartof',

            // create objects
            'INSERT INTO fzObject (id, class) values(1, "FaZend_Pos_Root")',
            'INSERT INTO fzObject (id, class) values(2, "Model_Pos_Car")',
            'INSERT INTO fzObject (id, class) values(3, "Model_Pos_Bike")',

            // create their snapshots
            // root
            'INSERT INTO fzSnapshot (fzObject, properties, version, alive, updated, baselined) ' . 
                'values(1, ' . $this->_dbAdapter->quote(serialize(array())) . ', 1, 1, ' . 
                $this->_dbAdapter->quote(Zend_Date::now()->getIso()). ', 0)',
            // car
            'INSERT INTO fzSnapshot (fzObject, properties, version, alive, updated, baselined) ' . 
                'values(2, ' . $this->_dbAdapter->quote(serialize(array('model'=>'bmw'))) . ', 1, 1, ' . 
                $this->_dbAdapter->quote(Zend_Date::now()->getIso()). ', 0)',
            // bike
            'INSERT INTO fzSnapshot (fzObject, properties, version, alive, updated, baselined) ' . 
                'values(3, ' . $this->_dbAdapter->quote(serialize(array('model'=>'kawasaki'))) . ', 1, 1, ' . 
                $this->_dbAdapter->quote(Zend_Date::now()->getIso()). ', 0)',

            // create links between them
            // root->car
            'INSERT INTO fzPartOf (parent, kid, name) values(1, 2, "car")',
            'INSERT INTO fzPartOf (parent, kid, name) values(2, 3, "bike")',
            );
        
        foreach ($queries as $query)
            $this->_dbAdapter->fetchAll($query);
            
        $car = FaZend_Pos_Abstract::root()->car;
        $this->assertTrue($car instanceof Model_Pos_Car, 'Car object was not retrieved');
        $this->assertEquals('bmw', $car->model, 'Car property is lost, why?');

        $bike = $car->bike;
        $this->assertTrue($bike instanceof Model_Pos_Bike, 'Bike object was not retrieved');
        $this->assertEquals('kawasaki', $bike->model, 'Bike property is lost, why?');
    }

}
