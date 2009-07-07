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
 * Robots.txt
 * 
 * @package Controllers
 */
class Fazend_RobotsController extends FaZend_Controller_Action {

    /**
     * Show the file
     *
     * @return void
     */
    public function indexAction() {

        $this->getResponse()->setHeader('Content-type', 'text/plain');
        $this->_helper->layout->disableLayout();
        $this->view->setFilter(null);

    }

}
