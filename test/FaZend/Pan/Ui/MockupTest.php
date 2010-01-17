<?php

require_once 'AbstractTestCase.php';

class FaZend_Pan_Ui_MockupTest extends AbstractTestCase
{
    
    public function testMockupCreationWorks()
    {
        $mockup = new FaZend_Pan_Ui_Mockup('index/mockup');
        $this->assertNotEquals(false, $mockup->png());
    }

}
