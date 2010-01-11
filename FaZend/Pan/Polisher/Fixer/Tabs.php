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
     **/
    protected $_types = array(
        'php',
        'phtml',
        'xml',
        );
    
    /**
     * Replacers
     *
     * @var string[]
     **/
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
