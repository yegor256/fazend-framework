<?php

require_once 'AbstractTestCase.php';

if (!class_exists('BuildException', false)) {
    class BuildException extends Exception { }
}

class FaZend_Application_Phing_CodeSnifferReportTest extends AbstractTestCase 
{
    
    public function testSimpleScenarioWorks() 
    {
        // we need this message since the operations below
        // might take some good time.
        logg('CodeSnifferReport testing started...');
        
        //$xml = tempnam(TEMP_PATH, 'phpcs');
        $xml = 'test.xml';
        $srcDir = APPLICATION_PATH;
        $destDir = $xml . '-output';
    
        // remove the directory before everything
        shell_exec('rm -rf ' . escapeshellarg($destDir));
        mkdir($destDir);
    
        // create phpcs report
        $result = shell_exec('/usr/local/bin/phpcs --report=xml ' . 
            escapeshellarg($srcDir) .
            ' >' . escapeshellarg($xml));
        $this->assertTrue(!$result, $result);
    
        require_once 'FaZend/Application/Phing/CodeSnifferReport.php';
        $reporter = new CodeSnifferReport();
        $reporter->init();
        $reporter->setxmlFile($xml);
        $reporter->setsrcDir($srcDir);
        $reporter->setdestDir($destDir);
        $reporter->main();
        
        // remove the directory after all
        shell_exec('rm -rf ' . escapeshellarg($xml . '*'));
    }

    public function testCodeQualityCollectorWorks() 
    {
        require_once 'FaZend/Application/Phing/CodeSnifferReport/CodeQuality.php';
        $quality = new CodeQuality();
        $quality->collect(APPLICATION_PATH . '/bootstrap.php');
        
        $this->assertTrue(is_integer($quality->revision), 'Revision is empty, why?');
        $this->assertTrue(strlen($quality->author) > 0, 'Author is empty, why?');
        $this->assertTrue(strlen($quality->log) > 0, 'Log is empty, why?');
    }

}
