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
 * Index controller
 *
 * Dispatcher of all pages/actions
 *
 * @package application
 * @subpackage controllers
 */
class IndexController extends FaZend_Controller_Action {

    /**
     * Test _redirectFlash() method
     *
     * @return void
     */
    public function flashAction() {
        $this->_redirectFlash('That works');
    }    

    /**
     * Test htmlTable helper
     *
     * @return void
     */
    public function tableAction() {
        FaZend_Paginator::addPaginator(Model_Owner::retrieveAll(), $this->view, 0);
    }

}
