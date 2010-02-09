<?php

require_once 'AbstractTestCase.php';

class FaZend_CallbackTest extends AbstractTestCase
{
    
    public function testTypeCastersWork()
    {
        $this->assertEquals(15, FaZend_Callback::factory('integer')->call(15.77));
        $this->assertEquals('11.1', FaZend_Callback::factory('string')->call(11.1));
        $this->assertEquals(15.66, FaZend_Callback::factory('float')->call(15.66));
        $this->assertEquals(true, FaZend_Callback::factory('boolean')->call(15));
    }

    public function testConstantWorks()
    {
        $this->assertEquals(true, FaZend_Callback::factory(true)->call(15.77));
    }

    public function testEvalCallbackWorks()
    {
        $this->assertEquals(
            't:1', 
            FaZend_Callback::factory('sprintf(${1}, ${2})')->call('t:%s', 1)
        );
        $this->assertTrue(
            FaZend_Callback::factory('new Zend_Date(${1});')->call('10 June 2010')
            instanceof Zend_Date
        );
    }

    public function testLambdaFunctionCallbackWorks()
    {
        $this->assertEquals(
            't:1', 
            FaZend_Callback::factory(create_function('$a, $b', 'return sprintf($a, $b);'))
            ->call('t:%s', 1)
        );
    }

}
        