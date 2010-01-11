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
 * Abstract code fixer
 *
 * @package Pan
 * @subpackage Polisher
 */
abstract class FaZend_Pan_Polisher_Fixer_Abstract
{
    
    /**
     * List of types of files that are accepted here
     *
     * @var string[]
     **/
    protected $_types = array();
    
    /**
     * Fix given content
     *
     * @param string File content
     * @param string File type (e.g. PHTML, PHP, XML, etc)
     * @return boolean Changes were made?
     */
    abstract public function fix(&$content, $type);

    /**
     * Get an array of all fixers
     *
     * @return FaZend_Pan_Polisher_Fixer_Abstract[]
     **/
    public static function retrieveAll() 
    {
        $list = array();
        foreach (new DirectoryIterator(dirname(__FILE__)) as $file) {
            if ($file->isDot())
                continue;
            if ($file == 'Abstract.php')
                continue;
            if (!preg_match('/\.php/', $file))
                continue;
            $class = 'FaZend_Pan_Polisher_Fixer_' . pathinfo($file, PATHINFO_FILENAME);
            $list[] = new $class();
        }
        return $list;
    }
    
    /**
     * This type is fixable by this Fixer?
     *
     * @param string File type (e.g. PHTML, PHP, XML, etc)
     * @return boolean
     **/
    public function isFixable($type) 
    {
        return in_array(strtolower($type), $this->_types);
    }

}
