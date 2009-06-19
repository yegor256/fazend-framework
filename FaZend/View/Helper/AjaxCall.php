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
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_AjaxCall extends FaZend_View_Helper {

	/**
	 * Show some text and replace it with the result of ajax call
	 *
	 * @return void
	 */
	public function ajaxCall($url, $message = 'loading...') {

	        // prototype is required for this	
	        $this->getView()->headScript()->appendFile($this->getView()->url(array('script'=>'prototype.js'), 'js', true));

		// ajas function for loading content
	        $this->getView()->headScript()->appendFile($this->getView()->url(array('script'=>'ajaxCall.js'), 'js', true));

	        $id = 'ajax' . (microtime(true) * 10000);

		// call when possible
	        $this->getView()->headScript()->appendScript("ajaxCall('{$id}', '{$url}');");

		return "<div id='{$id}' style='display: inline;'>{$message}</div>";
	}

}
