<?php
/**
 * @version $Id: UserControllerTest.php 1797 2010-04-04 08:59:17Z yegor256@gmail.com $
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
