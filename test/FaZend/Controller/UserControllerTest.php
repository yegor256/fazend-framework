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

class FaZend_Controller_UserControllerTest extends AbstractTestCase {

	public function setUp() {
		parent::setUp();

		Zend_Db_Table_Abstract::getDefaultAdapter()->query(
			"insert into user values (null, 'good@fazend.com', 'good')");

	}
	
	public function testLoginFormIsVisible () {
		$this->dispatch('/');
		$this->assertQuery('input#email', "Error in HTML: ".$this->getResponse()->getBody());
	}

	public function testWrongLoginIsProcessed () {
		$this->request->setPost(array(
			'email' => 'wrong@fazend.com',
			'pwd' => 'wrong',
		));
		$this->request->setMethod('POST');

		$this->dispatch('/');
		$this->assertQuery('ul.errors', "Error in HTML: ".$this->getResponse()->getBody());
	}

	public function testWrongPasswordIsProcessed () {
		$this->request->setPost(array(
			'email' => 'good@fazend.com',
			'pwd' => 'wrong',
		));
		$this->request->setMethod('POST');

		$this->dispatch('/');
		$this->assertQuery('ul.errors', "Error in HTML: ".$this->getResponse()->getBody());
	}

	public function testCorrectLoginIsProcessed () {
		$this->request->setPost(array(
			'email' => 'good@fazend.com',
			'pwd' => 'good',
		));
		$this->request->setMethod('POST');

		$this->dispatch('/');
		$this->assertQueryContentContains('a', 'logout', "Error in HTML: ".$this->getResponse()->getBody());
	}

	public function testLogoutWorks () {
		$this->dispatch('/user/logout');
	}

}
