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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'AbstractTestCase.php';

class TestClass {
    public $id;
    public function __construct($param1, $param2) {
        $this->id = $param1 . $param2;
    }
}

/**
 * FaZend_Flyweight test
 *
 * @package test
 */
class FlyweightTest extends FaZend_Test_TestCase {

    public function testMechanismWorks() {
        $object1 = FaZend_Flyweight::factory('TestClass', 'A', 'B');
        $object2 = FaZend_Flyweight::factory('TestClass', 'C', 'D');
        
        $object1copy = FaZend_Flyweight::factory('TestClass', 'A', 'B');
        $this->assertEquals($object1, $object1copy);

        $object2copy = FaZend_Flyweight::factory('TestClass', 'C', 'E');
        $this->assertNotEquals($object2, $object2copy);
    }

}