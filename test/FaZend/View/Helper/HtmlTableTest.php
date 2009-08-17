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

class FaZend_View_Helper_HtmlTableTest extends AbstractTestCase {
    
    /**
    * Test table rendering
    *
    */
    public function testHtmlTableWorks () {

        $this->dispatch('/index/table');

        $this->assertNotEquals(false, (bool)$this->getResponse()->getBody(), "Empty HTML instead of table, why?");

        $this->assertQuery('table', "Error in HTML: " . $this->getResponse()->getBody());

    }

}
