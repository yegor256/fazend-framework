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
		if (FaZend_User::isLoggedIn()) {
			FaZend_User::getCurrentUser()->logOut();
		}	

		$this->dispatch('/');
		$this->assertQuery('input#email', "Error in HTML: ".$this->getResponse()->getBody());
	}

	public function testWrongLoginIsProcessed () {
		if (FaZend_User::isLoggedIn()) {
			FaZend_User::getCurrentUser()->logOut();
		}	

		$this->request->setPost(array(
			'email' => 'wrong@fazend.com',
			'pwd' => 'wrong',
		));
		$this->request->setMethod('POST');

		$this->dispatch('/');
		$this->assertQuery('ul.errors', "Error in HTML: ".$this->getResponse()->getBody());
	}

	public function testWrongPasswordIsProcessed () {
		if (FaZend_User::isLoggedIn()) {
			FaZend_User::getCurrentUser()->logOut();
		}	

		$this->request->setPost(array(
			'email' => 'good@fazend.com',
			'pwd' => 'wrong',
		));
		$this->request->setMethod('POST');

		$this->dispatch('/');
		$this->assertQuery('ul.errors', "Error in HTML: ".$this->getResponse()->getBody());
	}

	public function testCorrectLoginIsProcessed () {
		if (FaZend_User::isLoggedIn()) {
			FaZend_User::getCurrentUser()->logOut();
		}	

		$this->request->setPost(array(
			'email' => 'good@fazend.com',
			'pwd' => 'good',
		));
		$this->request->setMethod('POST');

		$this->dispatch('/');
		$this->assertQueryContentContains('a', 'logout', "Error in HTML: ".$this->getResponse()->getBody());
	}

	public function testLogoutWorks () {
		$user = FaZend_User::findByEmail('good@fazend.com');
		$user->logIn();

		$this->dispatch('/user/logout');
		$this->assertEquals(false, FaZend_User::isLoggedIn());
	}

	public function testDoubleLogoutWorks () {
		if (FaZend_User::isLoggedIn()) {
			FaZend_User::getCurrentUser()->logOut();
		}	

		$this->dispatch('/user/logout');
		$this->assertEquals(false, FaZend_User::isLoggedIn());
	}

}
