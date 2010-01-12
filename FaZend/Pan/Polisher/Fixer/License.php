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

/**
 * Make proper file license, for the given file
 *
 * @package Pan
 * @subpackage Polisher
 */
class FaZend_Pan_Polisher_Fixer_License extends FaZend_Pan_Polisher_Fixer_Abstract
{
    
    /**
     * List of types of files that are accepted here
     *
     * @var string[]
     **/
    protected $_types = array(
        'php',
        'phtml',
        'css',
        );
        
    /**
     * License information
     *
     * - title
     * - text
     *
     * @var array
     **/
    protected $_license;    
    
    /**
     * Inject license
     *
     * @return void
     **/
    public function setLicense($title, array $lines) 
    {
        $this->_license = array(
            'title' => $title,
            'lines' => $lines,
            );
    }
    
    /**
     * Fix given content
     *
     * @param string File content
     * @param string File type (e.g. PHTML, PHP, XML, etc)
     * @return boolean
     * @throws FaZend_Pan_Polisher_Fixer_License_NoDocBlockFound
     */
    public function fix(&$content, $type)
    {
        switch ($type) {
            case 'php':
                $regex = '/^<\?php\s*\n\/\*\*\s*\n(.*?)\*\/\s*\n/ms';
                break;
            case 'css':
                $regex = '/^\/\*\*\s*\n(.*?)\*\/\s*\n/ms';
                break;
            case 'phtml':
                $regex = '/^<!--\s*\n(.*?)-->\n/ms';
                break;
        }
        
        if (!preg_match($regex, $content, $matches)) {
            FaZend_Exception::raise(
                'FaZend_Pan_Polisher_Fixer_License_NoDocBlockFound',
                'File phpDoc block not found',
                'FaZend_Pan_Polisher_FixerException'
            );
        }    
        $lines = explode("\n", $matches[1]);
        
        // ignore all leading empty lines
        for ($i=0; $i<count($lines); $i++) {
            if (trim($lines[$i]) != '*')
                break;
        }
        
        $empty = false;
        $blockCount = 0;
        // find the second empty line, ignoring any leading empty lines
        // $i found is the number of the line AFTER the license place
        for (; $i<count($lines); $i++) {
            if (trim($lines[$i]) != '*') {
                if ($empty)
                    $blockCount++;
            }
            $empty = (trim($lines[$i]) == '*');
            if ($blockCount == 2)
                break;
            if (preg_match('/^\s\*\s@\w/', $lines[$i]))
                break;
        }
        
        $license = $this->_getLicense();
        
        switch ($type) {
            case 'php':
                $docBlock = 
                "<?php\n" .
                "/**\n" .
                " * {$license['title']}\n" .
                " *\n" . 
                " * " . implode("\n * ", $license['lines']) . "\n" .
                " *\n" . 
                implode("\n", array_slice($lines, $i)) .
                "*/\n\n";
                break;
            case 'css':
                $docBlock = 
                "/**\n" .
                " * {$license['title']}\n" .
                " *\n" . 
                " * " . implode("\n * ", $license['lines']) . "\n" .
                " *\n" . 
                implode("\n", array_slice($lines, $i)) .
                "*/\n\n";
                break;
            case 'phtml':
                $docBlock = 
                "<!--\n" .
                " *\n" .
                " * {$license['title']}\n" .
                " *\n" . 
                " * " . implode("\n * ", $license['lines']) . "\n" .
                " *\n" . 
                implode("\n", array_slice($lines, $i)) .
                "-->\n";
                break;
        }
        
        $new = $docBlock . substr($content, strlen($matches[0]));
        if ($new == $content)
            return false;
            
        $content = $new;
        return true;
    }
    
    /**
     * Get license for the project
     *
     * @return void
     * @throws FaZend_Pan_Polisher_Fixer_License_LicenseFileAbsent
     **/
    protected function _getLicense() 
    {
        if (isset($this->_license))
            return $this->_license;
        
        if (defined('LICENSE_FILE'))
            $path = LICENSE_FILE;
        else
            $path = APPLICATION_PATH . '/../../LICENSE.txt';
        
        if (!file_exists($path)) {
            FaZend_Exception::raise(
                'FaZend_Pan_Polisher_Fixer_License_LicenseFileAbsent',
                "LICENSE.txt not found here: {$path}",
                'FaZend_Pan_Polisher_FixerException'
            );
        }
        
        $lines = explode("\n", file_get_contents($path));
        for ($i = 2; $i<count($lines); $i++) {
            if (!trim($lines[$i]))
                break;
        }
            
        return $this->_license = array(
            'title' => trim($lines[0]),
            'lines' => array_slice($lines, 2, $i-2)
            );
    }

}
