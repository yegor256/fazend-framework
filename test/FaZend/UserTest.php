<?php

require_once 'AbstractTestCase.php';

class FaZend_UserTest extends AbstractTestCase
{
    
    public function tearDown()
    {
        FaZend_User::logOut();
        parent::tearDown();
    }

    public function testSimpleScenarioWorks()
    {
        if (FaZend_User::isLoggedIn()) {
            $user = FaZend_User::getCurrentUser();
        } else {
            $user = FaZend_User::register('test@fazend.com', 'test');
            $user->login();
        }

        $this->assertNotEquals(false, $user->email);
        $this->assertNotEquals(false, $user->isCurrentUser());

        $this->assertTrue(FaZend_User::isLoggedIn());
    }

}
