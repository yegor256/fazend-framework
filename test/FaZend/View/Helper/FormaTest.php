<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_FormaTest extends AbstractTestCase
{

    public function testFormaWorks()
    {
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch('/index/forma');
        $this->assertNotEquals(
            false,
            (bool)$this->getResponse()->getBody(),
            "Empty HTML instead of forma, why?"
        );
        $this->assertQuery(
            'form',
            "Error in HTML: {$this->getResponse()->getBody()}"
        );
    }

    public function testCompletelyFilledFormRedirects()
    {
        $this->request->setPost(
            array(
                'name'   => 'John Doe',
                'client' => '1',
                // 'address' => '', this field is hidden, no value here
                'reason' => 'just no reason',
                'file'   => __FILE__,
                'sex'    => 'f',
                'submit' => 'go',
            )
        );
        $this->request->setMethod('POST');
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch('/index/forma');

        $this->assertQueryContentContains(
            'pre',
            '++success++', // this text is in Model_Owner::register()
            "Failure of the form: {$this->getResponse()->getBody()}"
        );
        $this->assertRedirectTo(
            '/index/submitted',
            "Not redirected, but returned: {$this->getResponse()->getBody()}"
        );
    }

}
