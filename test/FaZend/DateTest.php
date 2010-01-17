<?php

require_once 'AbstractTestCase.php';

class FaZend_DateTest extends AbstractTestCase
{
    
    public function testBasicFunctionsWork()
    {
        $date = FaZend_Date::make(time());
        $start = clone $date;
        $start->sub(1, Zend_Date::MONTH);
        
        $this->assertTrue($date->isBetween($start, $date));
    }

}
        