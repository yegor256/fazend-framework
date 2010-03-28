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
        if (isset(self::$_revision))
            return self::$_revision;
        
        $revFile = APPLICATION_PATH . '/deploy/subversion/revision.txt';
        if (file_exists($revFile))
            return self::$_revision = file_get_contents($revFile);
        
        $info = shell_exec('svn info ' . APPLICATION_PATH);
        if (preg_match('/Revision:\s(\d+)/m', $info, $matches))
            return self::$_revision = $matches[1] . 'L';
            
        return self::$_revision = 'local';
    }

}
