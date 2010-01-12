<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
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
class CodeSnifferReport extends Task 
{

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
    public function init() 
    {
    }

    /**
     * Executes
     * 
     * @return void
     * @throws BuildException
     */
    public function main() 
    {
        $this->_xml = simplexml_load_file($this->_xmlFile);
        if (!$this->_xml)
            throw new BuildException("Failed to open '{$this->_xmlFile}'");    
        
        // get the overall project quality
        $quality = $this->_getQuality($this->_srcDir, $this->_destDir);

        // copy styles.css
        $this->_createFile($this->_destDir . '/styles.css', 'styles.css', array());
        
        if (!@rename($this->_destDir . '/' . pathinfo($this->_srcDir, PATHINFO_BASENAME) . '.html', $this->_destDir . '/index.html'))
            throw new BuildException("Failed to rename '{$this->_destDir}/index.html'");    
    }

    /**
     * Setter
     *
     * @param string Name of XML file, after PHPCS
     * @return void
     */
    public function setxmlFile($xmlFile) 
    {
        $this->_xmlFile = $xmlFile;
    }
    
    /**
     * Setter
     *
     * @param string Directory with sources
     * @return void
     */
    public function setsrcDir($srcDir) 
    {
        $this->_srcDir = $srcDir;
    }
    
    /**
     * Setter
     *
     * @param string Destination directory
     * @return void
     */
    public function setdestDir($destDir) 
    {
        $this->_destDir = $destDir;
    }
    
    /**
     * Calculate metrics for the given directory
     *
     * @param string Absolute path of the source directory or file
     * @param string Absolute path of the destination directory
     * @return array ['lines', 'errors', 'warnings']
     */
    protected function _getQuality($sourceCode, $htmlOutputDirectory) 
    {
        // create new holder of quality metrics
        require_once dirname(__FILE__) . '/CodeSnifferReport/CodeQuality.php';
        $quality = new CodeQuality();
        
        $header = substr($sourceCode, strlen($this->_srcDir));
        
        // name of the processed node (file or dir)
        $nodeName = $this->_makeName(pathinfo($sourceCode, PATHINFO_BASENAME));
        
        if (is_dir($sourceCode)) {
            // make directory for HTML files
            $destDirectory = $htmlOutputDirectory . '/' . $nodeName;
            mkdir($destDirectory);
            
            $childs = array();
            // we go file by file and analyze them all
            foreach(scandir($sourceCode) as $childFile) {
                if ($childFile[0] == '.')
                    continue;
                // we attach metrics to our array
                $childQuality = $this->_getQuality($sourceCode . '/' . $childFile, $destDirectory);
                if ($childQuality !== false)
                    $quality->merge($childQuality);
                $childs[$childFile] = $childQuality;
            }

            // we create a summary directory listing for this particular dir
            $this->_createFile(
                $htmlOutputDirectory . '/' . $nodeName . '.html',
                'directory.html',
                array(
                    'childs' => $childs,
                    'header' => $header,
                    'name' => $nodeName,
                    'quality' => $quality,
                    ));
        } else {
            // here we do real calculation of metrics
            $info = $this->_xml->xpath("//file[@name='{$sourceCode}']");
            
            // this file is absent in the PHPCS report
            if (!$info)
                return false;
            
            // set information from PHPCS (sent to us in XML file)
            $quality->setInfo($info[0]);
            
            // collect quality information
            $quality->collect($sourceCode);
            
            // we save source code with our marks into HTML directory given in $htmlOutputDirectory
            $this->_createFile(
                $htmlOutputDirectory . '/' . $nodeName . '.html',
                'file.html',
                array(
                    'info' => $info[0],
                    'header' => $header,
                    'quality' => $quality,
                    'source' => $sourceCode,
                    ));
        }

        return $quality;
    }
    
    /**
     * Create HTML file from template
     *
     * @return void
     **/
    protected function _createFile($htmlFile, $template, $vars) 
    {
        ob_start();
        include(dirname(__FILE__) . '/phpcs-template/' . $template);
        $html = ob_get_clean();
        if (!file_put_contents($htmlFile, $html))
            throw new BuildException("Failed to save '{$htmlFile}'");    
    }
    
    /**
     * Cut email
     *
     * @return string
     **/
    protected function _cutEmail($email) 
    {
        if (strpos($email, '@') === false)
            return $email;
        return substr($email, 0, strpos($email, '@') + 1) . '...';
    }
    
    /**
     * Show quality in %
     *
     * @param float Quality
     * @return string
     **/
    protected function _showQuality($quality) 
    {
        return "<span style='color: " . 
            ($quality > 80 ? 
                'green' : ($quality > 60 ? 
                    'orange' : 'red'))
            . "'>" . sprintf('%0.1f', $quality) . '%</span>';
    }
    
    /**
     * Convert real file name to the label file
     *
     * @param string File name
     * @return string
     **/
    protected function _makeName($fileName) 
    {
        return str_replace('.', '-', $fileName);
    }

}
