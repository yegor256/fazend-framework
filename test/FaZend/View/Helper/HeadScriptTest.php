<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_HeadScriptTest extends AbstractTestCase
{
    
    public function testHeadScriptWorks()
    {
        $this->dispatch('/index/headscript');
    }

}
