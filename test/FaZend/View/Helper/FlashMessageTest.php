<?php

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_FlashMessageTest extends AbstractTestCase
{
    
    public function testHelperWorks()
    {
        $this->dispatch('/page_is_absent_for_sure');
        $this->assertRedirect("It wasn't redirect, why?");
    }

}
