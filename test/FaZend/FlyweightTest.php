<?php

require_once 'AbstractTestCase.php';

class TestClass
{
    public $id;
    public function __construct($param1, $param2)
    {
        $this->id = $param1 . $param2;
    }
}

class FlyweightTest extends AbstractTestCase
{

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

}