<?php

require_once 'AbstractTestCase.php';

class TestClass {
    public $id;
    public function __construct($param1, $param2) {
        $this->id = $param1 . $param2;
    }
}

class FlyweightTest extends FaZend_Test_TestCase
{

    public function testMechanismWorks()
    {
        $object1 = FaZend_Flyweight::factory('TestClass', 'A', 'B');
        $object2 = FaZend_Flyweight::factory('TestClass', 'C', 'D');
        
        $object1copy = FaZend_Flyweight::factory('TestClass', 'A', 'B');
        $this->assertEquals($object1, $object1copy);

        $object2copy = FaZend_Flyweight::factory('TestClass', 'C', 'E');
        $this->assertNotEquals($object2, $object2copy);
    }

}