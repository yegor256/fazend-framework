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
 * Replace tabs with 4-spaces
 *
 * @package Pan
 * @subpackage Polisher
 */
class FaZend_Pan_Polisher_Fixer_Tabs extends FaZend_Pan_Polisher_Fixer_Abstract
{
    
    /**
     * List of types of files that are accepted here
     *
     * @var string[]
     */
    protected $_types = array(
        'php',
        'phtml',
        'xml',
        );
    
    /**
     * Replacers
     *
     * @var string[]
     */
    protected $_replacers = array(
        '/\t/' => '    ',
        '/\r\n/' => "\n",
        );
    
    /**
     * Fix given content
     *
     * @param string File content
     * @param string File type (e.g. PHTML, PHP, XML, etc)
     * @return boolean
     */
    public function fix(&$content, $type)
    {
        $new = preg_replace(array_keys($this->_replacers), $this->_replacers, $content);
        
        // no changes?
        if ($new == $content)
            return false;
            
        $content = $new;
        return true;
    }

}
