<?php
/**
 * @version $Id$
 */

/**
 * @see AbstractTestCase
 */
require_once 'AbstractTestCase.php';

class FaZend_app_controllers_CssControllerTest extends AbstractTestCase
{

    public function testSingleCssIsVisible()
    {
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch('/__fz/css-123/index.css');
        $this->assertNotRedirect();
        $this->assertController('css');
        $this->assertAction('index');
    }

}
