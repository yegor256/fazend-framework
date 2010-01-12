<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
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
        $this->assertQuery('input#email', "Error in HTML: " . $this->getResponse()->getBody());
    }

    public function testWrongLoginIsProcessed () {
        FaZend_User::logOut();

        $this->request->setPost(array(
            'email' => 'wrong@fazend.com',
            'pwd' => 'wrong',
            'login' => 'Login',
        ));
        $this->request->setMethod('POST');

        $this->dispatch('/');
        $this->assertQuery('ul.errors', "Error in HTML: " . $this->getResponse()->getBody());
    }

    public function testWrongPasswordIsProcessed () {
        FaZend_User::logOut();

        $this->request->setPost(array(
            'email' => 'good@fazend.com',
            'pwd' => 'wrong',
            'login' => 'Login',
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
            'login' => 'Login',
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
