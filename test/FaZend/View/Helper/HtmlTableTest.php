<?php

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_HtmlTableTest extends AbstractTestCase
{
    
    public function testHtmlTableWorks()
    {
        $this->dispatch('/index/table');
        $this->assertNotEquals(false, (bool)$this->getResponse()->getBody(), "Empty HTML instead of table, why?");
        $this->assertQuery('table', "Error in HTML: " . $this->getResponse()->getBody());
    }

}
