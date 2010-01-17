<?php

require_once 'AbstractTestCase.php';

class FaZend_Controller_AdmControllerTest extends AbstractTestCase
{

    public function testSchemaIsVisible()
    {
        $this->dispatch($this->view->url(array('action'=>'schema'), 'adm', true));
        $this->assertQuery('pre', "Error in HTML: ".$this->getResponse()->getBody());
    }

    public function testAllUrlsWork()
    {
        $this->dispatch($this->view->url(array('action'=>'squeeze'), 'adm', true));
        $this->dispatch($this->view->url(array('action'=>'log'), 'adm', true));
        $this->dispatch($this->view->url(array('action'=>'tables'), 'adm', true));
        $this->dispatch($this->view->url(array('action'=>'backup'), 'adm', true));
    }

    public function testCustomActionWorks()
    {
        $this->dispatch($this->view->url(array('action'=>'custom'), 'adm', true));
        $this->assertQuery('p.ok', "Failed to run custom action");
    }

}
