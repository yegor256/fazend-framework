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
