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
     **/
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
