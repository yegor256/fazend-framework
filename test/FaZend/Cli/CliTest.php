<?php

require_once 'AbstractTestCase.php';

class FaZend_Cli_CliTest extends AbstractTestCase
{
    
    public function testCliCallsAreProcessed ()
    {
        $param = rand(100, 999);

        chdir(APPLICATION_PATH . '/public');
        $result = shell_exec('php index.php OSVersion --param=' . $param . ' 2>&1');

        $this->assertNotEquals(false, $result, "Empty result, why?");
    }

}
        