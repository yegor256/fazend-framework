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
