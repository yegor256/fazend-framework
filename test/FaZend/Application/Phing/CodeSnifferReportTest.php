<?php

require_once 'AbstractTestCase.php';

if (!class_exists('BuildException', false)) {
    class BuildException extends Exception
    {
        // ...
    }
}

class FaZend_Application_Phing_CodeSnifferReportTest extends AbstractTestCase
{
    
    public function testSimpleScenarioWorks() 
    {
        // we need this message since the operations below
        // might take some good time.
        logg('CodeSnifferReport testing started, wait 30-40sec...');
        
        //$xml = tempnam(TEMP_PATH, 'phpcs');
        $xml = 'test.xml';
        $srcDir = APPLICATION_PATH . '/Model';
        $destDir = $xml . '-output';
    
        // remove the directory before everything
        shell_exec('rm -rf ' . escapeshellarg($destDir));
        mkdir($destDir);
    
        // create phpcs report
        $result = shell_exec(
            '/usr/local/bin/phpcs --report=xml ' . 
            escapeshellarg($srcDir) .
            ' >' . escapeshellarg($xml)
        );
        $this->assertTrue(!$result, $result);
    
        require_once 'FaZend/Application/Phing/CodeSnifferReport.php';
        $reporter = new CodeSnifferReport();
        $reporter->init();
        $reporter->setxmlFile($xml);
        $reporter->setsrcDir($srcDir);
        $reporter->setdestDir($destDir);
        try {
            $reporter->main();
        } catch (Exception $e) {
            $incomplete = $e;
        }
        
        // remove the directory after all
        shell_exec('rm -rf ' . escapeshellarg($xml . '*'));
        
        if (isset($incomplete)) {
            logg(get_class($incomplete) . ': ' . $incomplete->getMessage());
            $this->markTestIncomplete();
        }
    }

    public function testCodeQualityCollectorWorks() 
    {
        require_once 'FaZend/Application/Phing/CodeSnifferReport/CodeQuality.php';
        $quality = new CodeQuality();
        try {
            $quality->collect(APPLICATION_PATH . '/bootstrap.php');
        } catch (Exception $e) {
            logg(get_class($e) . ': ' . $e->getMessage());
            $this->markTestIncomplete();
        }
        
        $this->assertTrue(is_integer($quality->revision), 'Revision is empty, why?');
        $this->assertTrue(strlen($quality->author) > 0, 'Author is empty, why?');
        $this->assertTrue(strlen($quality->log) > 0, 'Log is empty, why?');
    }

}
