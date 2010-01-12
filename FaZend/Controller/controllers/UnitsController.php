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

/**
 * Test organizer
 *
 * @package controllers
 */
class Fazend_UnitsController extends FaZend_Controller_Panel {

    /**
     * Sanity check before dispatching
     *
     * @return void
     */
    public function preDispatch() {
        
        // sanity check
        if (APPLICATION_ENV == 'production')
            $this->_redirectFlash('Units controller is not allowed in production environment', 'restrict', 'login');
        
        parent::preDispatch();

    }

    /**
     * Show the map of unit tests
     *
     * @return void
     */
    public function indexAction() {

        $this->view->tests = FaZend_Pan_Tests_Manager::getInstance()->getTests();

    }

    /**
     * Run one test and return result
     *
     * @return void
     */
    public function runAction() {

        $this->_returnJSON(FaZend_Pan_Tests_Manager::getInstance()->factory($this->getRequest()->getPost('name'))->run());

    }

    /**
     * Get result of the "running" test
     *
     * @return void
     */
    public function routineAction() {

        $this->_returnJSON(FaZend_Pan_Tests_Manager::getInstance()->factory($this->getRequest()->getPost('name'))->result());

    }

    /**
     * Stop current test
     *
     * @return void
     */
    public function stopAction() {

        $this->_returnJSON(FaZend_Pan_Tests_Manager::getInstance()->factory($this->getRequest()->getPost('name'))->stop());

    }

}
