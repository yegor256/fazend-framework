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

class FaZend_UserTest extends AbstractTestCase {
    
    public function tearDown() {
        FaZend_User::logOut();
        parent::tearDown();
    }

    public function testSimpleScenarioWorks () {

        if (FaZend_User::isLoggedIn()) {
            $user = FaZend_User::getCurrentUser();
        } else {
            $user = FaZend_User::register('test@fazend.com', 'test');
            $user->login();
        }

        $this->assertNotEquals(false, $user->email);

        $this->assertNotEquals(false, $user->isCurrentUser());
    }

}
