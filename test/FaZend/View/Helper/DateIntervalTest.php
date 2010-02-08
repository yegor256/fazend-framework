<?php

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_DateIntervalTest extends AbstractTestCase
{
    
    public function testHelperWorks()
    {
        $helper = new FaZend_View_Helper_DateInterval();
        $this->assertTrue(is_string($helper->dateInterval(new Zend_Date('10-10-2009'))));
    }

}
