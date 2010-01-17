<?php

require_once 'AbstractTestCase.php';

class FaZend_Cli_cli_BaselineTest extends AbstractTestCase
{
    
    public function testWeCanBaselineOurCode()
    {
        chdir(APPLICATION_PATH . '/public');
        $result = shell_exec('php index.php Baseline --email=team@fazend.com --dry-run 2>&1');

        $this->assertNotEquals(false, $result, "Empty result, why?");
        
        logg('Baseline returned: ' . $result);
    }

}
        