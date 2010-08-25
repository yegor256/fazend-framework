<?php
/**
 * @version $Id$
 */

/**
 * @see AbstractTestCase
 */
require_once 'AbstractTestCase.php';

class FaZend_app_cli_FzBackupTest extends AbstractTestCase
{

    public function testSimpleScenario()
    {
        $backup = FaZend_Backup::getInstance();
        $backup->setOptions(
            array(
                'execute' => true,
            )
        );

        require_once FAZEND_APP_PATH . '/cli/FzBackup.php';
        $cli = new FzBackup();
        
        ob_start();
        $result = $cli->execute();
        $log = ob_get_clean();
        
        $this->assertEquals(FaZend_Cli_Abstract::RETURNCODE_OK, $result);
        logg($log);
    }

}
