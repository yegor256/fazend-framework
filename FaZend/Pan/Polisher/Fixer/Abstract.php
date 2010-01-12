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
