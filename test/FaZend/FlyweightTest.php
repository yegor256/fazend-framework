<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
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