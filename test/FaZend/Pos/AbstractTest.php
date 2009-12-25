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
    
        $car->test = new FaZend_StdObject();
        $something = $car->something; // exception here
    }
    
    /**
     * @expectedException FaZend_Pos_Properties_ItemMissed
     */
    public function testUnassignedArrayItemReturnsNull()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        FaZend_Pos_Abstract::root()->car = $car = new Model_Pos_Car();
    
        $car->test = new FaZend_StdObject();
        $something = $car[1]; // exception here
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
    
        $result = $this->_dbAdapter->fetchAll("SELECT * FROM fzSnapshot WHERE fzObject = {$car->ps()->id}");
    
        // 4 snapshots: 2 for root and two for the object
        $this->assertEquals(3, count($result),
            'FaZend_Pos_Abstrast::save() did not create unique versions');
    }
    
    public function testSerializeObjectSavesSnapshotOnSerialize()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;
    
        $car->make  = 'Nissan';
        $car->model = 'Maxima';
        $car->active = false;
        serialize($car);
    
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
    
        $serialized = serialize($car);
    
        $car->active = true;
        $car->ps()->save();
    
        $car2 = unserialize($serialized);
        $this->assertTrue($car2->active, 'Unserialized object did not recieve updated property values');
    }
    
    public function testThereIsOnlyOneRoot()
    {
        FaZend_Pos_Abstract::cleanPosMemory();
        FaZend_Pos_Abstract::root()->car = new Model_Pos_Car();
    
        for ($i=0; $i<10; $i++) {
            FaZend_Pos_Abstract::cleanPosMemory();
            $car = FaZend_Pos_Abstract::root()->car;
        }
        $result = $this->_dbAdapter->fetchAll("SELECT * FROM fzSnapshot WHERE fzObject={$car->ps()->id}");
        $this->assertEquals(1, count($result), 'Root object produces many snapshots when created');
    }
    
    public function testObjectCanHaveSubObjects() {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;
        
        $car->bike = new Model_Pos_Bike();
        $car->bike->owners = array('Jim', 'Nick');
        
        $car->bike->price = '1670 USD';
        $car->bike->ps()->save();
        FaZend_Pos_Abstract::cleanPosMemory();
    
        $bike = FaZend_Pos_Abstract::root()->car->bike;
        $this->assertEquals($bike->price, '1670 USD', 'Object is lost, why?');
        $this->assertTrue(count($bike->owners) == 2, 'Array inside the object is lost, why?');
    }
    
    public function testObjectWorksAsArray() {
        FaZend_Pos_Abstract::cleanPosMemory();
        FaZend_Pos_Abstract::root()->carArray = $car = new Model_Pos_Car();
        
        $car[] = 1;
        $car[] = 1;
        $car[] = 1;
        $car['test'] = 2;
        $car['test2'] = 5;
        
        $this->assertEquals(5, $car['test2'], 'Array item is lost, why?');
        $this->assertEquals(5, count($car), 'Array has invalid number of elements, why?');
        
        $counter = 0;
        foreach ($car as $name=>$value) {
            $counter++;
        }
        $this->assertEquals(5, $counter, 'FOREACH cannot reach all elements, why?');

        $this->assertEquals(5, count($car->getArrayCopy()), 'getArrayCopy() did not return an array, why?');
    }
    
    public function testGetPropertiesWork() {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;
        
        $car->test = 1;
        $car->test2 = 2;
        $this->assertEquals(2, count($car->ps()->properties), 'List of properties is broken, why?');
    }
        
    public function testDumpWorks() {
        FaZend_Pos_Abstract::cleanPosMemory();
        FaZend_Pos_Abstract::root()->car = $car = new Model_Pos_Car();
        $dump = $car->ps()->dump(false);
    }
    
    public function testLinksBetweenObjectsCanByCycled() {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;
        
        $bike = new Model_Pos_Bike();
        FaZend_Pos_Abstract::root()->bike = $bike;
        
        $car->bike = $bike;
        $bike->car = $car;
        
        $car2 = $car->bike->car->bike->car->bike->car->bike->car; // should work
    }
    
    public function testLinkCanLeadToItself() {
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->car = $car;
        $car->car = $car;
        
        $car2 = $car->car->car->car->car->car; // should work
    }
    
    public function testObjectsCanBeLinkedThroughMediators() {
        FaZend_Pos_Abstract::cleanPosMemory();
        FaZend_Pos_Abstract::root()->car = $car = new Model_Pos_Car();
        FaZend_Pos_Abstract::root()->bike = $bike = new Model_Pos_Bike();
        
        $car->holder = new FaZend_StdObject();
        $car->holder->bike = $bike;
        $car->ps()->save();
        FaZend_Pos_Abstract::cleanPosMemory();
    
        $car = FaZend_Pos_Abstract::root()->car;
        $bike = $car->holder->bike;
    }
    
    /**
     * @expectedException FaZend_Pos_SerializationProhibited 
     */
    public function testLostObjectsCantBeLinked() {
        FaZend_Pos_Abstract::cleanPosMemory();
        echo "\nThis test will throw exception FaZend_Pos_SerializationProhibited when all tests are finished, it's OK\n";
        FaZend_Pos_Abstract::root()->car = $car = new Model_Pos_Car();
        
        $car->holder = $holder = new FaZend_StdObject();
        $car->holder->bike = $bike = new Model_Pos_Bike();
        $car->ps()->save();
    }
    
    public function testObjectsCanBeStandalone() {
        $car = new Model_Pos_Car();
        unset($car);
    }
    
    /**
     * @expectedException FaZend_Pos_Exception
     */
    public function testObjectCantBeManagedOutsideOfPos() {
        $car = new Model_Pos_Car();
        $car->mode = 'bmw'; // we expect an exception here
    }
    
    public function testObjectIsAnIterator() {
        FaZend_Pos_Abstract::cleanPosMemory();
        FaZend_Pos_Abstract::root()->carIterator = $car = new Model_Pos_Car();
    
        for ($i = 0; $i<10; $i++)
            $car[$i] = $i;
            
        $this->assertEquals(10, count($car), 'Array has invalid number of elements, why?');
        foreach ($car as $name=>$item) {
            $this->assertEquals($name, $item, "Strange key/value of the element: '$name'/'$item', why?");
        }
        
        // it fails, and I don't know how to fix it...
        $c = current($car);
        $this->assertTrue(is_integer($c), "Current() fails, why? Class: " . get_class($c));
    }
    
    public function testObjectCanBeVeryDeep() {
        FaZend_Pos_Abstract::cleanPosMemory();
        
        $obj = FaZend_Pos_Abstract::root()->car = new Model_Pos_Car();
    
        for ($i = 0; $i<5; $i++) {
            $obj->obj = new Model_Pos_Bike();
            $obj = $obj->obj;
        }
        
        FaZend_Pos_Abstract::root()->ps()->save();
    }
    
    public function testEndlessCyclesAreUnderstoodProperly() {
        FaZend_Pos_Abstract::cleanPosMemory();
        
        $car = FaZend_Pos_Abstract::root()->car = new Model_Pos_Car();
    
        $obj = new FaZend_StdObject();
        $obj->link = $obj;
        
        $car->obj = $obj;
    
        FaZend_Pos_Abstract::root()->car->ps()->save();
    
        // clean and retrieve it back
        FaZend_Pos_Abstract::cleanPosMemory();
        $car = FaZend_Pos_Abstract::root()->car->obj->link->link->link->link->link;
    }
    
    public function testEndlessCyclesWithMeditatorsWork() {
        FaZend_Pos_Abstract::cleanPosMemory();
        
        $car = FaZend_Pos_Abstract::root()->car = new Model_Pos_Car();
    
        $obj = new FaZend_StdObject();
        $car->obj = $obj;
        $obj->car = $car;
    
        FaZend_Pos_Abstract::root()->ps()->save();
        FaZend_Pos_Abstract::root()->car->ps()->save();
        
        // clean and retrieve it back
        FaZend_Pos_Abstract::cleanPosMemory();
        
        $car1 = FaZend_Pos_Abstract::root()->car;
        $obj1 = $car1->obj;
        
        $car2 = $obj1->car;
        $obj2 = $car2->obj;
        
        // Cars are different
        $this->assertNotEquals(spl_object_hash($car1), spl_object_hash($car2));
        
        // But their internal structures are THE SAME!
        $this->assertEquals(spl_object_hash($obj1), spl_object_hash($obj2));
    
        $this->assertEquals(spl_object_hash($car1->ps()->parent), spl_object_hash($car2->ps()->parent), 
            'Why both parents are not root?');
        
        $car3 = $obj2->car;
        $this->assertEquals(spl_object_hash($car2->ps()->parent), spl_object_hash($car3->ps()->parent), 
            'Why both parents are not root?');
        
        $obj3 = $car3->obj;
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
                'values(3, ' . $this->_dbAdapter->quote(serialize(array(
                    'model' => 'kawasaki',
                    FaZend_Pos_Properties::ARRAY_PREFIX . 'code' => 'test',
                    ))) . ', 1, 1, ' . 
                $this->_dbAdapter->quote(Zend_Date::now()->getIso()). ', 0)',
    
            // create links between them
            // root->car
            'INSERT INTO fzPartOf (parent, kid, name) values(1, 2, "car")',
            'INSERT INTO fzPartOf (parent, kid, name) values(2, 3, "bike")',
            'INSERT INTO fzPartOf (parent, kid, name) values(2, 3, "' . FaZend_Pos_Properties::ARRAY_PREFIX . 'item")',
            );
        
        foreach ($queries as $query)
            $this->_dbAdapter->fetchAll($query);
            
        $car = FaZend_Pos_Abstract::root()->car;
        
        // $this->assertTrue(FaZend_Pos_Abstract::root()->ps()->isClean(), 'Root is not clean?');
        $this->assertTrue($car instanceof Model_Pos_Car, 'Car object was not retrieved');
        $this->assertEquals('bmw', $car->model, 'Car property is lost, why?');
    
        $bike = $car->bike;
        $this->assertTrue($bike instanceof Model_Pos_Bike, 'Bike object was not retrieved');
        $this->assertEquals('kawasaki', $bike->model, 'Bike property is lost, why?');
        $this->assertEquals('test', $bike['code'], 'Bike CODE is lost, why?');
        $this->assertTrue(isset($bike->model), 'Why MODEL is not set?');
        $this->assertTrue(isset($bike['code']), 'Why CODE is not set?');
        
        // one item should be there, by the key ITEM and value = bike
        foreach ($car as $name=>$value) {
            $this->assertEquals('item', $name, 'Item name is not correct, why?');
            $this->assertTrue($value instanceof Model_Pos_Bike, 'Value is not loaded, why? Class: ' . get_class($value));
            $this->assertEquals('test', $value['code'], 'Bike CODE is lost in array item, why?');
        }
    }
    
    public function tearDown() 
    {
        parent::tearDown();
        try {
            FaZend_Pos_Abstract::root()->ps()->saveAll();
        } catch (FaZend_Pos_SerializationProhibited $e) {
            // ignore them, since they are results of special tests above
        }
    }

}
