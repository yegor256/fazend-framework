<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class CallbackFoo
{
    public static function one($a)
    {
        return $a;
    }
    public function two($a)
    {
        return $a;
    }
}

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
            FaZend_Callback::factory('sprintf(${a1}, ${a2})')->call('t:%s', 1)
        );
        $this->assertTrue(
            FaZend_Callback::factory('new Zend_Date(${a1});')->call('10 June 2010')
            instanceof Zend_Date
        );
    }
    
    public function testMethodCallbackWorks()
    {
        $this->assertEquals(
            't', 
            FaZend_Callback::factory(array('CallbackFoo', 'one'))->call('t')
        );
        $this->assertEquals(
            'ee',
            FaZend_Callback::factory(array(new CallbackFoo(), 'two'))->call('ee')
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
    
    public function testClosureCallbackWorks()
    {
        $this->assertEquals(
            't:1', 
            FaZend_Callback::factory(function($a, $b) { return sprintf($a, $b); })
            ->call('t:%s', 1)
        );
    }
    
    public function testInputsAreListedCorrectly()
    {
        $this->assertEquals(array('integer'), FaZend_Callback::factory('integer')->getInputs());
        $this->assertEquals(array('string'), FaZend_Callback::factory('string')->getInputs());
        $this->assertEquals(array('float'), FaZend_Callback::factory('float')->getInputs());
        $this->assertEquals(array('boolean'), FaZend_Callback::factory('boolean')->getInputs());
    
        $this->assertEquals(
            array('a1'), 
            FaZend_Callback::factory('new Zend_Date(${a1}, ${i1})')->getInputs()
        );
    
        $this->assertEquals(
            array('a', 'b'), 
            FaZend_Callback::factory(create_function('$a, $b', 'return false;'))->getInputs()
        );
    
        $this->assertEquals(
            array('money'), 
            FaZend_Callback::factory(array(new FaZend_Bo_Money(), 'sub'))->getInputs()
        );
    }
    
    public function testInjectionsWork()
    {
        $this->assertEquals(
            'tt250', 
            FaZend_Callback::factory('"{${a1}}{${i1}}"')
            ->inject(250)
            ->call('tt')
        );
    }
    
    /**
     * @expectedException FaZend_Callback_Eval_MissedInjectionException
     */
    public function testMissedInjectionIsDetected()
    {
        $this->assertEquals(
            'tt250', 
            FaZend_Callback::factory('"{${a1}}{${i1}}"')
            ->call('tt')
        );
    }
    
    /**
     * @expectedException FaZend_Callback_Eval_MissedArgumentException
     */
    public function testMissedArgumentIsDetected()
    {
        $this->assertEquals(
            'tt250', 
            FaZend_Callback::factory('"{${a1}}{${i1}}"')
            ->inject('222')
            ->call()
        );
    }

    public function testPotentialMemoryLeaks()
    {
        $start = memory_get_usage();
        for ($i = 0; $i < 20; $i++) {
            $d = FaZend_Callback::factory('${a1}')->call(time());
            $lost = memory_get_usage() - $start;
        }
        $this->assertLessThan(
            15 * 1024, 
            $lost, 
            "We've lost {$lost} bytes, why?"
        );
    }
}
        