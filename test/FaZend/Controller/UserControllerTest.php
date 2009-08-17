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

        $this->_dbAdapter->query(
            "insert into user values (null, 'good@fazend.com', 'good')");

    }

    public function tearDown() {
        FaZend_User::logOut();
        parent::tearDown();
    }

    public function testLoginFormIsVisible () {
        FaZend_User::logOut();

        $this->dispatch('/index');
        $this->assertQuery('input#email', "Error in HTML: ".$this->getResponse()->getBody());
    }

    public function testWrongLoginIsProcessed () {
        FaZend_User::logOut();

        $this->request->setPost(array(
            'email' => 'wrong@fazend.com',
            'pwd' => 'wrong',
            'submit' => 'Login',
        ));
        $this->request->setMethod('POST');

        $this->dispatch('/');
        $this->assertQuery('ul.errors', "Error in HTML: ".$this->getResponse()->getBody());
    }

    public function testWrongPasswordIsProcessed () {
        FaZend_User::logOut();

        $this->request->setPost(array(
            'email' => 'good@fazend.com',
            'pwd' => 'wrong',
            'submit' => 'Login',
        ));
        $this->request->setMethod('POST');

        $this->dispatch('/');
        $this->assertQuery('ul.errors', "Error in HTML: ".$this->getResponse()->getBody());
    }

    public function testCorrectLoginIsProcessed () {
        FaZend_User::logOut();

        $this->request->setPost(array(
            'email' => 'good@fazend.com',
            'pwd' => 'good',
            'submit' => 'Login',
        ));
        $this->request->setMethod('POST');

        $this->dispatch('/');
        $this->assertQueryContentContains('a', 'logout', "Error in HTML: ".$this->getResponse()->getBody());
    }

    public function testLogoutWorks () {
        $user = FaZend_User::findByEmail('good@fazend.com');
        $user->logIn();

        $this->dispatch($this->view->url(array('action'=>'logout'), 'user', true));
        $this->assertEquals(false, FaZend_User::isLoggedIn());
    }

    public function testDoubleLogoutWorks () {
        FaZend_User::logOut();

        $this->dispatch($this->view->url(array('action'=>'logout'), 'user', true));
        $this->assertEquals(false, FaZend_User::isLoggedIn());
    }

}
