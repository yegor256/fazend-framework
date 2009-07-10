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
 * Link to a static file, in "views/files" directory
 *
 *
 */
class FaZend_View_Helper_ViewFile extends FaZend_View_Helper {

    /**
     * File in views/files directory
     *
     * @param string Path of the file, from /public directory
     * @return string URL of the file
     */
    public function viewFile($file) {

        //trim the file name (just in case)
        $file = trim($file);

        return $this->getView()->url(array('file'=>$file), 'file', true);

    }

}
