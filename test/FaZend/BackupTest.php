<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_BackupTest extends AbstractTestCase
{
    
    public function testItIsASingleton()
    {
        $backup = FaZend_Backup::getInstance();
        $this->assertTrue($backup === FaZend_Backup::getInstance());
    }
    
    public function testBackupWorks()
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
        