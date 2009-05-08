<?php
/**
 *
 * Copyright (c) 2009, FaZend.com
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of FaZend.com. located at
 * www.FaZend.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@FaZend.com
 *
 * @copyright Copyright (c) FaZend.com, 2009
 * @version $Id$
 *
 */

/**
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 */
class FaZend_View_Helper_StaticFile {

	public function staticFile($file) {
	        //trim the file name (just in case)
		$file = trim($file);

	        //front controller
        	$frontController = Zend_Controller_Front::getInstance();

	        //base url for this application
	        $baseUrl = $frontController->getBaseUrl();

	        return WEBSITE_URL.$baseUrl . '/' . $file;
	}

}
