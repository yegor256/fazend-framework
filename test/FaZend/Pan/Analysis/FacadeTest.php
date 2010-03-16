<?php

require_once 'AbstractTestCase.php';

class FaZend_Pan_Analysis_FacadeTest extends AbstractTestCase
{
    
    public function testListOfComponentsIsAccessible()
    {
        $facade = new FaZend_Pan_Analysis_Facade();
        $list = $facade->getComponentsList();
        
        $items = array();
        foreach ($list as $component) {
            $items[] = sprintf(
                '%s (%s)', 
                $component['fullName'],
                $component['type']
            );
        }
        $this->assertTrue(count($items) > 0, 'empty list of components, why?');
        logg("Full list of found elements:\n\t" . implode("\n\t", $items));
        
        $component = array_pop($list);
        logg(print_r($component, true));
    }

    public function testListOfTodoTagsIsNotEmpty()
    {
        $facade = new FaZend_Pan_Analysis_Facade();
        $list = $facade->getComponentsList();
        
        $tags = array();
        foreach ($list as $component) {
            $tags = array_merge($tags, $component['todo']);
        }
        
        $this->assertTrue(count($tags) > 0, 'empty list of @todo tags, why?');
        logg("Full list of found tags: " . implode(", ", $tags));
        
    }

}
