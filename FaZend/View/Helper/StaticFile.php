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
 * Simplified link for a static file
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 */
class FaZend_View_Helper_StaticFile {

    /**
     * Simplified link for a static file
     *
     * @param string Path of the file, from /public directory
     * @return string URL of the file
     */
    public function staticFile($file) {

        //trim the file name (just in case)
        $file = trim($file);

        //front controller
        $frontController = Zend_Controller_Front::getInstance();

        //base url for this application
        $baseUrl = $frontController->getBaseUrl();

        return WEBSITE_URL . $baseUrl . '/' . $file;

    }

}
