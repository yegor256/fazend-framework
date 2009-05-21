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

class FaZend_Controller_AdmControllerTest extends AbstractTestCase {

	public function setUp() {
		parent::setUp();

	}
	
	public function testSchemaIsVisible () {

		$this->dispatch('/adm/schema');
		$this->assertQuery('pre', "Error in HTML: ".$this->getResponse()->getBody());

	}

}
