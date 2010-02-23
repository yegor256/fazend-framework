<?php

require_once 'AbstractTestCase.php';

class FaZend_Bo_NameTest extends AbstractTestCase
{
    
    public static function providerNames()
    {
        return array(
            array("Mr. John\tSmith", 'John', 'Smith'),
            array('Angela Meredith', 'Angela', 'Meredith'),
            array('Mrs. Nicola Young', 'Nicola', 'Young'),
            array('Peter Coehn', 'Peter', 'Coehn'),
            array('Victor S. K. DeNiro', 'Victor', 'DeNiro'),
            array('Johnny Depp', 'Johnny', 'Depp'),
        );
    }

    /**
     * @dataProvider providerNames
     */
    public function testWeCanParseDifferentNames($name, $first, $last)
    {
        $class = new FaZend_Bo_Name($name);
        $this->assertTrue(
            $class instanceof FaZend_Bo_Name,
            "Format can't be parsed: '{$name}', why?"
        );
        $this->assertEquals(
            $first, 
            $class->first,
            "Invalid conversion of {$name}, first name is {$class->first}, why?"
        );
        $this->assertEquals(
            $last, 
            $class->last,
            "Invalid conversion of {$name}, last name is {$class->last}, why?"
        );
    }

    /**
     * @expectedException FaZend_Bo_Name_EmptyException
     */
    public function testEmptyNameThrowsException()
    {
        $name = new FaZend_Bo_Name('Mr.   ');
    }
    
}

