<?php

require_once 'AbstractTestCase.php';

class FaZend_Pos_PropertiesTest extends AbstractTestCase 
{   

    public function setUp()
    {
        parent::setUp();
        $this->_user = FaZend_User::register('test2', 'test2');
        FaZend_Pos_Properties::setUserId($this->_user->__id);
        FaZend_Pos_Properties::cleanPosMemory();
    }

    public function tearDown()
    {
        parent::tearDown();
        try {
            FaZend_Pos_Properties::cleanPosMemory();
        } catch (FaZend_Pos_SerializationProhibited $e) {
            // ignore them, since they are results of special tests above
            logg('Exception in tearDown(): ' . $e->getMessage());
        }
    }

    public function testCanRetreiveLastEditor()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->save();

        $actual = $car->ps()->editor->email;
        $expected = $this->_user->email;

        $this->assertEquals($expected, $actual, "Expected '$expected' user, but received '$actual', why?");
    }

    /**
     * @expectedException FaZend_Pos_Exception
     */
    public function testCannotSetLastEditor()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->save();

        $car->ps()->editor = 'test';
    }

    public function testCanRetrieveLatestVersionNumber()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->model = 'test';
        $car->ps()->save();

        $this->assertEquals( 2, $car->ps()->version );
        $car->model = 'test2';
        $car->ps()->save();

        $this->assertEquals( 3, $car->ps()->version );
    }

    /**
     * @expectedException FaZend_Pos_Exception
     */
    public function testCannotSetLastVersionNumber()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->version = 1;
    }

    public function testCanRetrieveLastUpdatedTimestamp()
    {
        $start = new Zend_Date();
        
        sleep(1);
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->save();
        
        $timestamp = $car->ps()->updated;
        //TODO we can't actually test that the time is accurate without
        //architectural changes.

        $this->assertTrue($start->isEarlier($timestamp), 'Start time was not earlier than updated');
    }

    /**
     * @expectedException FaZend_Pos_Exception
     */
    public function testCannotSetLastUpdatedTimestamp()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->updated = time();
    }

    public function testCanCleanArray()
    {
        FaZend_Pos_Properties::root()->car = $car = new Model_Pos_Car();
        $car[1] = 'test';
        $car[2] = 'test2';
        $car->ps()->save();
        
        FaZend_Pos_Properties::cleanPosMemory();
        FaZend_Pos_Properties::root()->car->ps()->cleanArray();
        $this->assertTrue(count(FaZend_Pos_Properties::root()->car) == 0);
    }

    public function testCanGetIdOfObject()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->save();

        $bike = new Model_Pos_Bike();
        FaZend_Pos_Properties::root()->bike = $bike;
        $bike->ps()->save();

        $this->assertGreaterThan(
            0, $car->ps()->id,
            'Id returned was not greater than 0'
        );
        $this->assertGreaterThan( 
            $car->ps()->id, $bike->ps()->id,
            'Second object\'s id was not greater than first object\'s'
        );
    }

    /**
     * @expectedException FaZend_Pos_Exception
     */
    public function testCannotSsetIdOfObject()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->id = 3;
    }

    public function testCanGetTypeOfObject()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;

        $this->assertEquals(
            $car->ps()->type,
            'Model_Pos_Car',
            'Returned type for Car object was not "Car"'
        );
    }

    /**
     * @expectedException FaZend_Pos_Exception
     */
    public function testCannotSetTypeOfObject()
    {
        $car = new Model_Pos_Car(); 
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->type = 'Bike';
    }

    public function testCanGetParent()
    {
        $car = new Model_Pos_Car(); 
        FaZend_Pos_Properties::root()->car = $car;
        $parent = $car->ps()->parent;
    }

    /**
     * @expectedException FaZend_Pos_Exception
     */
    public function testCannotSetParent()
    {
        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->ps()->parent = new Model_Pos_Bike();
    }

    public function testTouchOnlyUpdatesVersion()
    {
        $this->markTestIncomplete();
    }

    public function testWorkWithVersionReturnsCorrectSnapshot()
    {
        $make  = 'Lotus';
        $model = 'Elise 112 R';
        $status = 'inactive';

        $car = new Model_Pos_Car();
        FaZend_Pos_Properties::root()->car = $car;
        $car->make      = $make;
        $car->model     = $model;
        $car->status    = $status;
        $car->ps()->save();

        $version = $car->ps()->version;

        $car->ps()->touch();
        $car->ps()->touch();

        $car->status = 'active';
        $car->driver = 'John';

        // $car2 = $car->ps()->workWithVersion( $version );
        // 
        // $params = $car2->toArray();
        // $this->assertEquals( $params['make'], $make );
        // $this->assertEquals( $params['model'], $model );
        // $this->assertEquals( $params['status'], $status );
        // $this->assertArrayNotHasKey( 'driver', array_keys( $params ), 
        //         'Property driver was not in expected version' );
    }

    /**
     * @expectedException FaZend_Pos_Properties_PropertyMissed
     */
    public function testStatelessPropertyNeverSaved()
    {
        FaZend_Pos_Properties::root()->car556 = $car = new Model_Pos_Car();
        $car->ps()->setStatelessProperty('drivers');
        $car->drivers = array('John', 'Peter');
        $car->ps()->save();
        FaZend_Pos_Properties::cleanPosMemory();
        
        $drivers = FaZend_Pos_Properties::root()->car556->drivers;
    }

    /**
     * @expectedException FaZend_Pos_Properties_PropertyMissed
     */
    public function testStatelessPropertyEvenIfSavedNeverRestored()
    {
        FaZend_Pos_Properties::root()->car908 = $car = new Model_Pos_Car();
        $car->drivers = array('John', 'Peter');
        $car->ps()->save();
        FaZend_Pos_Properties::cleanPosMemory();
        
        $car = FaZend_Pos_Properties::root()->car908;
        $car->ps()->setStatelessProperty('drivers');
        $drivers = $car->drivers;
        $car->ps()->save();
        FaZend_Pos_Properties::cleanPosMemory();

        $car = FaZend_Pos_Properties::root()->car908;
        $drivers = $car->drivers;
    }

    public function testStatelessPropertyNormallyAccessible()
    {
        FaZend_Pos_Properties::root()->car1677 = $car = new Model_Pos_Car();
        $car->drivers = array('John', 'Peter');
        $car->ps()->setStatelessProperty('drivers');
        $this->assertEquals(2, count($car->drivers));
    }

}
