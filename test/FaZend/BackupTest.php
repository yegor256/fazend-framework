<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_BackupTest extends AbstractTestCase
{
    
    public function testBackupWorks ()
    {
        $backup = FaZend_Backup::getInstance();
        $backup->setOptions(
            array(
                'execute' => true,
            )
        );
        $backup->execute();
    }

}
        