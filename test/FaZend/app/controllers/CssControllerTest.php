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
        $this->dispatch('/__fz/css/index.css');
        $this->assertNotRedirect();
    }

}
