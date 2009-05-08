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
 * Convert string to nice URL string
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_NiceUrl {

	/**
	* Converts and returns
	*
	* @return string
	*/
	public function niceUrl($str) {

		return trim(preg_replace('/\_+/', '_', preg_replace('/[^\w\d]/', '_', ucwords($str))), '_') . '.html';
	}

}
