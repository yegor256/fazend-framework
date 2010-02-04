<?php

require_once 'AbstractTestCase.php';

class TestClass
{
    public $id;
    public function __construct($param1, $param2 = false)
    {
        // $this->id = $param1 . $param2;
    }
}

require_once 'FaZend/Flyweight.php';
class testFlyweight extends FaZend_Flyweight
{
    public static function buildId($args)
    {
        return self::_makeId($args);
    }
}

class FlyweightTest extends AbstractTestCase
{

    public function testObjectIdsAreUnique()
    {
        $args = array(
            array('test'),
            array('A', new FaZend_StdObject()),
            array('A', new FaZend_StdObject()),
        );
        $ids = array();
        foreach ($args as $objects)
            $ids[] = testFlyweight::buildId($objects);
            
        $this->assertEquals(count($ids), count(array_unique($ids)));
    }

    public function testMechanismWorks()
    {
        $object1 = FaZend_Flyweight::factory('TestClass', 'A', 'B');
        $object2 = FaZend_Flyweight::factory('TestClass', 'C', 'D');
        
        $object1copy = FaZend_Flyweight::factory('TestClass', 'A', 'B');
        $this->assertTrue($object1 === $object1copy);
    
        $object2copy = FaZend_Flyweight::factory('TestClass', 'C', 'E');
        $this->assertFalse($object2 === $object2copy);
    }
    
    public function testObjectsCanBeExplicitlyIdentified()
    {
        $object1 = FaZend_Flyweight::factoryById('TestClass', 12, 'A', 'B');
        $object2 = FaZend_Flyweight::factoryById('TestClass', 12, 'C', 'D');
        $this->assertTrue($object1 === $object2);
    }
    
    public function testObjectInstancesMakeDifference()
    {
        $obj1 = FaZend_Flyweight::factory('TestClass', 'A', $a = new FaZend_StdObject());
        $obj2 = FaZend_Flyweight::factory('TestClass', 'A', $b = new FaZend_StdObject());
        $this->assertFalse($obj1 === $obj2, 'Objects are the same, why?');
    }

}