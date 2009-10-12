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

require_once 'FaZend/Controller/Action.php';

/**
 *
 * 
 * @package controllers
 */
class Fazend_FazendjsController extends FaZend_Controller_Action {

    /**
     *
     * @return void
     */
    public function indexAction() {

        $this->getResponse()->setHeader('Content-type', 'text/javascript');
        $this->_helper->layout->disableLayout();
        $this->view->setFilter(null);

        $this->_helper->viewRenderer
            ->setViewScriptPathSpec(':controller/'.$this->_getParam('script'));

        $this->_helper->viewRenderer($this->_getParam('script'));

    }

}
