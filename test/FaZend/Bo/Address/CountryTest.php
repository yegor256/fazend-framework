<?php

require_once 'AbstractTestCase.php';

class FaZend_Bo_Address_CountryTest extends AbstractTestCase
{
    
    public static function providerNames()
    {
        return array(
            array("US", 'US'),
            array('UK', 'UK'),
        );
    }

    /**
     * @dataProvider providerNames
     */
    public function testWeCanParseDifferentNames($name, $code)
    {
        $class = new FaZend_Bo_Address_Country($name);
        $this->assertTrue(
            $class instanceof FaZend_Bo_Address_Country,
            "Format can't be parsed: '{$name}', why?"
        );
        $this->assertEquals(
            $code, 
            $class->code,
            "Invalid conversion of {$name}, code is {$class->code}, why?"
        );
    }
    
}

