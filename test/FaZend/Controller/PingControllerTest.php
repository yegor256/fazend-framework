<?php

require_once 'AbstractTestCase.php';

class FaZend_Controller_PingControllerTest extends AbstractTestCase
{

    public function testIndexIsVisible()
    {
        $this->dispatch($this->view->url(array(), 'ping', true));
    }

}
