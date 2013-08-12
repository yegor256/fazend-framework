<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_FlashMessageTest extends AbstractTestCase
{

    public function testHelperWorks()
    {
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch('/page_is_absent_for_sure');
        $this->assertRedirect("It wasn't redirect, why?");
    }

}
