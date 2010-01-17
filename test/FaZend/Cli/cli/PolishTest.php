<?php

require_once 'AbstractTestCase.php';

class FaZend_Cli_cli_PolishTest extends AbstractTestCase
{
    
    public function testWeCanMakeDryRun()
    {
        chdir(APPLICATION_PATH . '/public');
        $result = shell_exec('php index.php Polish --dry-run 2>&1');

        $this->assertNotEquals(false, $result, "Empty result, why?");
        logg('Polisher returned: ' . $result);
    }

}
        