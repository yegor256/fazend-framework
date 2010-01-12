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

class FaZend_Controller_AdmControllerTest extends AbstractTestCase {

    public function setUp() {
        parent::setUp();

    }
    
    public function testSchemaIsVisible () {

        $this->dispatch($this->view->url(array('action'=>'schema'), 'adm', true));
        $this->assertQuery('pre', "Error in HTML: ".$this->getResponse()->getBody());

    }

    public function testAllUrlsWork () {

        $this->dispatch($this->view->url(array('action'=>'squeeze'), 'adm', true));
        $this->dispatch($this->view->url(array('action'=>'log'), 'adm', true));
        $this->dispatch($this->view->url(array('action'=>'tables'), 'adm', true));
        $this->dispatch($this->view->url(array('action'=>'backup'), 'adm', true));

    }

    public function testCustomActionWorks () {

        $this->dispatch($this->view->url(array('action'=>'custom'), 'adm', true));
        $this->assertQuery('p.ok', "Failed to run custom action");

    }

}
