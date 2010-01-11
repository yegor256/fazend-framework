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
        'php'
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
     * Fix given content
     *
     * @param string File content
     * @param string File type (e.g. PHTML, PHP, XML, etc)
     * @return boolean
     * @throws FaZend_Pan_Polisher_Fixer_License_NoDocBlockFound
     */
    public function fix(&$content, $type)
    {
        $regex = '/^<\?php\n\/\*\*\n(.*?)\*\//ms';
        
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
            if (trim($lines[$i]) == '*')
                break;
        }
        
        $empty = false;
        $blockCount = 0;
        // find the second empty line, ignoring any leading empty lines
        for (; $i<count($lines); $i++) {
            if (trim($lines[$i]) != '*') {
                if ($empty)
                    $blockCount++;
            }
            $empty = (trim($lines[$i]) == '*');
            if ($blockCount == 3)
                break;
        }
        
        $license = $this->_getLicense();
        
        $docBlock = 
        "<?php\n" .
        "/**\n" .
        " * {$license['title']}\n" .
        " *\n" . 
        " * " . implode(' * ', $license['lines']) .
        " *\n" . 
        implode("\n", array_slice($lines, $i)) .
        "*/"
        ;
        
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
        
        $lines = file($path);
        for ($i = 2; $i<count($lines); $i++) {
            if (!trim($lines[$i], "\n\r\t "))
                break;
        }
            
        return $this->_license = array(
            'title' => trim($lines[0], "\n\r\t "),
            'lines' => array_slice($lines, 2, $i-2)
            );
    }

}
