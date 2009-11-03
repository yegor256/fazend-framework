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
class FaZend_Revision {

    /**
     * Get the number of SVN revision of the code
     *
     * @return string
     */
    public static function get() {
        $revFile = APPLICATION_PATH . '/deploy/subversion/revision.txt';
        return (file_exists ($revFile) ? file_get_contents ($revFile) : 'local');    
    }

}
