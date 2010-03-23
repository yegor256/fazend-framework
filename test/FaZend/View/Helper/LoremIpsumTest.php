<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_LoremIpsumTest extends AbstractTestCase
{
    
    public function testLoremIpsumHelperWorks()
    {
        $this->dispatch('/index/loremipsum');
        $this->assertQuery('p', "Error in HTML: " . $this->getResponse()->getBody());
    }

}
