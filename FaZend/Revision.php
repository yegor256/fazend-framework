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
 * Get SVN revision number for the product
 *
 * @package Revision
 */
class FaZend_Revision
{

    const VERSION = '0.2dev';
    
    /**
     * Name of the project
     *
     * @var string
     * @see FaZend_Application_Resource_Fazend::init()
     * @see setName()
     */
    protected static $_name;
    
    /**
     * Cached value
     *
     * @var string|integer
     */
    protected static $_revision;

    /**
     * Get the number of SVN revision of the code
     *
     * @return string
     */
    public static function get()
    {
        if (isset(self::$_revision)) {
            return self::$_revision;
        }
        
        $revFile = APPLICATION_PATH . '/deploy/subversion/revision.txt';
        if (file_exists($revFile)) {
            return self::$_revision = file_get_contents($revFile);
        }
        
        $info = FaZend_Exed::exec('svn info ' . APPLICATION_PATH . ' 2>&1');
        $matches = array();
        if (preg_match('/Revision:\s(\d+)/m', $info, $matches)) {
            return self::$_revision = $matches[1] . 'L';
        }
            
        return self::$_revision = 'local';
    }
    
    /**
     * Set name of the project
     *
     * @param string Name of the project
     * @return void
     * @see FaZend_Application_Resource_Fazend::init()
     */
    public static function setName($name) 
    {
        self::$_name = $name;
    }

    /**
     * Get name of the project
     *
     * @return string Name of the project
     */
    public static function getName() 
    {
        return self::$_name;
    }

}
