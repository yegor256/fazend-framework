<?php

require_once 'AbstractTestCase.php';

class FaZend_Cli_cli_PanTest extends AbstractTestCase
{
    
    public function testWeCanGetJSON()
    {
        chdir(APPLICATION_PATH . '/public');
        $result = shell_exec('php index.php Pan --pan=analysis 2>&1');

        $this->assertNotEquals(false, $result, "Empty result, why?");
        $json = Zend_Json::decode($result);
        
        logg('JSON returned: ' . count($json) . ': ' . cutLongLine($result));
    }

}
        