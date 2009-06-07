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
 * Resolver with patches
 *
 *
 */
class FaZend_Auth_Adapter_Http_Resolver_File extends Zend_Auth_Adapter_Http_Resolver_File {

	/**
	 * Resolve with patch
	 *
	 * @return value|false
	 */
	public function resolve($username, $realm) {

		return trim(parent::resolve($username, $realm), "\r\n\t ");

	}
}
