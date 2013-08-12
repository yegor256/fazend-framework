<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_LoremIpsumTest extends AbstractTestCase
{

    public function testLoremIpsumHelperWorks()
    {
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch('/index/loremipsum');
        $this->assertQuery('p', "Error in HTML: " . $this->getResponse()->getBody());
    }

}
