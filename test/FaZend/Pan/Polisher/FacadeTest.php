<?php

require_once 'AbstractTestCase.php';

class FaZend_Pan_Polisher_FacadeTest extends AbstractTestCase
{
    
    public function testDryRunWorks()
    {
        $facade = new FaZend_Pan_Polisher_Facade(APPLICATION_PATH, true, true);
        $facade->polish();
    }

}
