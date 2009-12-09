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

require_once 'phing/Task.php';

/**
 * Convert code sniffer full XML report into a collection of HTML files
 *
 * @package Application
 * @subpackage Phing
 */
class CodeSnifferReport extends Task {

    /**
     * XML file after PHPCS
     *
     * @var string
     */
    protected $_xmlFile = false;

    /**
     * Directory with source code
     *
     * @var string
     */
    protected $_srcDir = false;

    /**
     * Destination directory, to store HTML files
     *
     * @var string
     */
    protected $_destDir = false;

    /**
     * XML content
     *
     * @var SimpleXml
     */
    protected $_xml = false;

    /**
     * Initiator (when the build.xml sees the task)
     * 
     * @return void
     */
    public function init() {
    }

    /**
     * Executes
     * 
     * @return void
     * @throws BuildException
     */
    public function main() {
        $this->_xml = simplexml_load_file($this->_xmlFile);
        if (!$this->_xml)
            throw new BuildException("Failed to open '{$this->_xmlFile}'");    
        $quality = $this->_getQuality($this->_srcDir, $this->_destDir);
    }

    /**
     * Setter
     *
     * @param string Name of XML file, after PHPCS
     * @return void
     */
    public function setxmlFile($xmlFile) {
        $this->_xmlFile = $xmlFile;
    }
    
    /**
     * Setter
     *
     * @param string Directory with sources
     * @return void
     */
    public function setsrcDir($srcDir) {
        $this->_srcDir = $srcDir;
    }
    
    /**
     * Setter
     *
     * @param string Destination directory
     * @return void
     */
    public function setdestDir($destDir) {
        $this->_destDir = $destDir;
    }
    
    /**
     * Calculate metrics for the given directory
     *
     * @return array ['lines', 'errors', 'warnings']
     */
    protected function _getQuality($sourceCode, $htmlOutputDirectory) {
        $quality = array(
            'lines' => 0,
            'errors' => 0,
            'warnings' => 0,
        );
        
        $header = substr($sourceCode, strlen($this->_srcDir));
        
        if (is_dir($sourceCode)) {
            // make directory for HTML files
            $destDirectory = $htmlOutputDirectory . '/' . pathinfo($sourceCode, PATHINFO_BASENAME);
            mkdir($destDirectory);
            
            $childs = array();
            // we go file by file and analyze them all
            foreach(scandir($sourceCode) as $childFile) {
                if ($childFile[0] == '.')
                    continue;
                // we attach metrics to our array
                $childQuality = $this->_getQuality($sourceCode . '/' . $childFile, $destDirectory);
                $quality['lines'] += $childQuality['lines'];
                $quality['errors'] += $childQuality['errors'];
                $quality['warnings'] += $childQuality['warnings'];
                $childs[$childFile] = $childQuality;
            }

            // we create a summary directory listing for this particular dir
            $this->_createFile(
                $htmlOutputDirectory . '/index.html',
                'directory.html',
                array(
                    'childs' => $childs,
                    'header' => $header,
                    'name' => pathinfo($destDirectory, PATHINFO_BASENAME),
                    'quality' => $quality,
                    ));
        } else {
            // here we do real calculation of metrics
            $info = $this->_xml->xpath("file[name='{$sourceCode}']");
            
            // this file is absent in the PHPCS report
            if (!$info)
                return $quality;
            
            $quality['errors'] = $info->attributes()->errors;
            $quality['warnings'] = $info->attributes()->warnings;
            
            // we save source code with our marks into HTML directory given in $htmlOutputDirectory
            $this->_createFile(
                $htmlOutputDirectory . '/' . pathinfo($sourceCode, PATHINFO_BASENAME) . '.html',
                'file.html',
                array(
                    'info' => $info,
                    'header' => $header,
                    'quality' => $quality,
                    ));
                    
            $quality['lines'] = 4; //test
        }

        return $quality;
    }
    
    /**
     * Create HTML file from template
     *
     * @return void
     **/
    protected function _createFile($htmlFile, $template, $vars) {
        ob_start();
        include(dirname(__FILE__) . '/phpcs-template/' . $template);
        $html = ob_get_clean();
        if (!file_put_contents($htmlFile, $html))
            throw new BuildException("Failed to save '{$htmlFile}'");    
    }
    
}
