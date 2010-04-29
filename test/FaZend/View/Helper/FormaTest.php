<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_FormaTest extends AbstractTestCase
{
    
    public function testFormaWorks()
    {
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
                'name' => 'John Doe',
                'client' => '1',
                'reason' => 'just no reason',
                'file' => 'test.jpg',
                'submit' => 'go',
            )
        );
        $this->request->setMethod('POST');
        
        $this->dispatch('/index/forma');
        $this->assertQueryContentContains(
            'pre', 
            '++success++', 
            "Failure of the form: {$this->getResponse()->getBody()}"
        );
        $this->assertRedirectTo(
            '/index/submitted',
            "Not redirected, but returned: {$this->getResponse()->getBody()}"
        );
    }

}
