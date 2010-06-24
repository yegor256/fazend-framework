<?php
/**
 * @version $Id: UserControllerTest.php 1797 2010-04-04 08:59:17Z yegor256@gmail.com $
 */

/**
 * @see AbstractTestCase
 */
require_once 'AbstractTestCase.php';

class FaZend_app_controllers_FileControllerTest extends AbstractTestCase
{

    public function testFileIsVisibleWithoutRendering()
    {
        $this->dispatch('/__fz/f/test.txt');
        $this->assertController('file', 'invalid controller dispatched');
        $this->assertAction('index', 'invalid action dispatched');
        $this->assertHeader('Content-type', '"Content-type" header is not set, why?');
        $this->assertNotRedirect();
    }

    public function testFileIsVisibleWithRendering()
    {
        $this->dispatch('/__fz/fr/test.txt');
        $this->assertController('file', 'invalid controller dispatched');
        $this->assertAction('index', 'invalid action dispatched');
        $this->assertHeaderContains('Content-type', 'plain/text');
        $this->assertNotRedirect();
    }

}
