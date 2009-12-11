<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

require_once 'AbstractTestCase.php';

class BuildException extends Exception { }

class FaZend_Application_Phing_CodeSnifferReportTest extends AbstractTestCase {
    
    public function testSimpleScenarioWorks() {
        //$xml = tempnam(TEMP_PATH, 'phpcs');
        $xml = 'test.xml';
        $srcDir = APPLICATION_PATH;
        $destDir = $xml . '-output';

        // remove the directory before everything
        shell_exec('rm -rf ' . escapeshellarg($destDir . '/*'));
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

}
