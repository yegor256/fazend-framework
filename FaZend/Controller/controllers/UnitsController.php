<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

/**
 * Test organizer
 *
 *
 */
class Fazend_UnitsController extends FaZend_Controller_Action {

    /**
     * Sanity check before dispatching
     *
     * @return void
     */
    public function preDispatch() {
        
        // sanity check
        if (APPLICATION_ENV == 'production')
            $this->_redirectFlash('Units controller is not allowed in production environment', 'index', 'index');
        
        // layout reconfigure to fazend
        $layout = Zend_Layout::getMvcInstance();
        $layout->setViewScriptPath(FAZEND_PATH . '/View/layouts/scripts');
        $layout->setLayout('units');

    }

    /**
     * Show the map of unit tests
     *
     * @return void
     */
    public function indexAction() {

        $this->view->tests = FaZend_Test_Manager::getInstance()->getTests();

    }

    /**
     * Run one test and return result
     *
     * @return void
     */
    public function runAction() {

        $this->_returnJSON(FaZend_Test_Manager::getInstance()->run($this->getRequest()->getPost('name')));

    }

    /**
     * Stop current test
     *
     * @return void
     */
    public function stopAction() {

        $this->_returnJSON(FaZend_Test_Manager::getInstance()->stop($this->getRequest()->getPost('name')));

    }

}
