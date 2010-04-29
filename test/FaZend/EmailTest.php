<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_EmailTest extends AbstractTestCase
{
    
    public function testEmailsAreRenderedAndSent()
    {
        $mailer = new Model_Email_StubMailer();
        Zend_Mail::setDefaultTransport($mailer);

        for ($i=0; $i<2; $i++) {
            $email = FaZend_Email::create('test.tmpl')
                ->set('toEmail', 'manager@fazend.com')
                ->set('subject', 'subject line')
                ->set('bodyText', 'test for body')
                ->set('cc', array('john@example.com' => 'John Doe'));
            $email->send();
            $email->send(true);
        }

        $this->assertNotEquals(false, $mailer->body, "Empty email generated, why?");
    }

}
        