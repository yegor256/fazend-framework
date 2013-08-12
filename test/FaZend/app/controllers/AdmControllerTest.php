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
        $this->markTestSkipped('due to a bug in Zend Test framework');
        $this->dispatch($uri);
        $this->assertNotRedirect();
        $this->assertController('adm', "Invalid controller at '{$uri}'");
        $this->assertAction('squeeze', "Invalid action at '{$uri}'");
    }

}
