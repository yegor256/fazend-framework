<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_HtmlTableTest extends AbstractTestCase
{

    public function testHtmlTableWorks()
    {
        $this->view->setFilter(null);
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch('/index/table');
        $body = $this->getResponse()->getBody();

        $this->assertNotEquals(
            false,
            (bool)$body,
            "Empty HTML instead of table, why?"
        );
        $this->assertQuery('table', "Table is not found here, why?: $body");
        $this->assertQuery('td', "TD is not found, why: $body");
        $this->assertQuery('tr', "TR is not found, why: $body");
        $this->assertQuery('th', "TH is not found, why: $body");

        $this->assertQuery(
            'td[style~="width:450px;"]',
            "some error in addColumnStyle(): $body"
        );
    }

}
