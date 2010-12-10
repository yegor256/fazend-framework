<?php
/**
 * @version $Id$
 */

/**
 * @see AbstractTestCase
 */
require_once 'AbstractTestCase.php';

class FaZend_app_controllers_AdmControllerTest extends AbstractTestCase
{

    public function testAllUrlsWork()
    {
        $uri = $this->view->url(array('action' => 'squeeze'), 'fz__adm', true);
        $this->assertNotRedirect();
        $this->assertController('adm');
        $this->assertController('squeeze');
        $this->dispatch($uri);
    }

}
