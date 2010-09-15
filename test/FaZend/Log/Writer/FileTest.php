<?php
/**
 * @version $Id$
 */

/**
 * @see AbstractTestCase
 */
require_once 'AbstractTestCase.php';

class FaZend_Log_Writer_FileTest extends AbstractTestCase
{

    public function testSimpleScenarioWorks()
    {
        $file = tempnam(TEMP_PATH, 'fz_writer-test-');
        $w = new FaZend_Log_Writer_File($file);
        $log = new Zend_Log($w);
        $log->info('should work');
        $saved = file_get_contents($file);
        unlink($file);

        $this->assertFalse(empty($saved));
    }
    
}
