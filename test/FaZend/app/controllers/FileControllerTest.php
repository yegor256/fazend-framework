<?php
/**
 * @version $Id$
 */

/**
 * @see AbstractTestCase
 */
require_once 'AbstractTestCase.php';

class FaZend_app_controllers_FileControllerTest extends AbstractTestCase
{

    public function testFileIsVisibleWithoutRendering()
    {
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch('/__fz/f-123/test.txt');
        $this->assertController('file', 'invalid controller dispatched');
        $this->assertAction('index', 'invalid action dispatched');
        $this->assertHeader('Content-type', '"Content-type" header is not set, why?');
        $this->assertNotRedirect();
    }

    public function testFileIsVisibleWithRendering()
    {
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch('/__fz/fr-123/test.txt');
        $this->assertController('file', 'invalid controller dispatched');
        $this->assertAction('index', 'invalid action dispatched');
        $this->assertHeaderContains('Content-type', 'plain/text');
        $this->assertNotRedirect();
    }

}
