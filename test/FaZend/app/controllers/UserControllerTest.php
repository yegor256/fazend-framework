<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_app_controllers_UserControllerTest extends AbstractTestCase
{

    public function setUp()
    {
        parent::setUp();
        // register a user, explicitly in DB
        $this->_dbAdapter->query("insert into user values (null, 'good@fazend.com', 'good')");
    }

    public function tearDown()
    {
        FaZend_User::logOut();
        parent::tearDown();
    }

    public function testLoginFormIsVisible()
    {
        FaZend_User::logOut();

        $this->dispatch('/index');
        $this->assertNotRedirect();
        $this->assertQuery('input#email', "Error in HTML: " . $this->getResponse()->getBody());
    }

    public function testWrongLoginIsProcessed()
    {
        FaZend_User::logOut();

        $this->request->setPost(
            array(
                'email' => 'wrong@fazend.com',
                'pwd' => 'wrong',
                'login' => 'Login',
            )
        );
        $this->request->setMethod('POST');

        $this->dispatch('/');
        $this->assertNotRedirect();
        // $this->assertQuery('ul.errors', "Error in HTML: " . $this->getResponse()->getBody());
    }

    public function testWrongPasswordIsProcessed()
    {
        FaZend_User::logOut();

        $this->request->setPost(
            array(
                'email' => 'good@fazend.com',
                'pwd' => 'wrong',
                'login' => 'Login',
            )
        );
        $this->request->setMethod('POST');

        $this->dispatch('/');
        $this->assertNotRedirect();
        $this->assertQuery('ul.errors', "Error in HTML: ".$this->getResponse()->getBody());
    }

    public function testCorrectLoginIsProcessed()
    {
        FaZend_User::logOut();

        $this->request->setPost(
            array(
                'email' => 'good@fazend.com',
                'pwd' => 'good',
                'login' => 'Login',
            )
        );
        $this->request->setMethod('POST');

        $this->dispatch('/');
        $this->assertNotRedirect();
        $this->assertQueryContentContains('a', 'logout', "Error in HTML: ".$this->getResponse()->getBody());
    }

    public function testLogoutWorks()
    {
        $user = FaZend_User::findByEmail('good@fazend.com');
        $user->logIn();

        $this->dispatch($this->view->url(array('action'=>'logout'), 'user', true));
        $this->assertEquals(false, FaZend_User::isLoggedIn());
    }

    public function testDoubleLogoutWorks()
    {
        FaZend_User::logOut();

        $this->dispatch($this->view->url(array('action'=>'logout'), 'user', true));
        $this->assertEquals(false, FaZend_User::isLoggedIn());
    }

}
