<?php

require_once 'AbstractTestCase.php';

class FaZend_Pan_Analysis_FacadeTest extends AbstractTestCase
{
    
    public function testListOfComponentsIsAccessible()
    {
        $facade = new FaZend_Pan_Analysis_Facade();
        $list = $facade->getComponentsList();
        $component = array_pop($list);
        logg(print_r($component, true));
    }

}
