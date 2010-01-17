<?php

require_once 'AbstractTestCase.php';

class FaZend_BackupTest extends AbstractTestCase
{
    
    public function testBackupWorks ()
    {
        $backup = new FaZend_Backup();
        $backup->execute();

        $log = $backup->getLog();

        $this->assertNotEquals(false, $log, "Empty log, why?");
    }

}
        