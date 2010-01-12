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

/**
 * Test case
 *
 * @package tests
 */
class FaZend_EmailTest extends AbstractTestCase {
    
    public function testEmailsAreRenderedAndSent () {

        $mailer = new Model_Email_StubMailer();
        Zend_Mail::setDefaultTransport($mailer);

        $email = FaZend_Email::create('test.tmpl')
            ->set('toEmail', 'manager@fazend.com')
            ->set('subject', 'subject line')
            ->set('bodyText', 'test for body');

        $email->send();
        $email->send(true);

        $this->assertNotEquals(false, $mailer->body, "Empty email generated, why?");

    }

}
        