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

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_ErrorMessageTest extends AbstractTestCase {
	
	/**
	* Test PNG rendering
	*
	*/
	public function testHelperWorks () {

		$this->dispatch('/page_is_absent_for_sure');

		$this->assertQuery('p[class*="error"]', 'error here: '.$this->getResponse()->getBody());

	}


}