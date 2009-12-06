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

class FaZend_View_Helper_FormaTest extends AbstractTestCase {
    
    /**
     * Test forma rendering
     *
     * @return void
     */
    public function testFormaWorks () {
        $this->dispatch('/index/forma');
        $this->assertNotEquals(false, (bool)$this->getResponse()->getBody(), "Empty HTML instead of table, why?");
        $this->assertQuery('form', "Error in HTML: " . $this->getResponse()->getBody());
    }

}
