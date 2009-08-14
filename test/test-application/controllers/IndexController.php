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
 *
 *
 */
class IndexController extends FaZend_Controller_Action {

    /**
     * Test total application
     *
     * @return void
     */
    public function indexAction() {

    }

    public function flashAction() {
        $this->_redirectFlash('Thats work');
    }    

    /**
     * Test htmlTable helper
     *
     * @return void
     */
    public function tableAction() {

        FaZend_Paginator::addPaginator(Model_Owner::retrieveAll(), $this->view, 0);
        
    }

    /**
     * Test squeezePNG helper
     *
     * @return void
     */
    public function squeezeAction() {

    }
    
    /**
     * HeadScriptTest
     *
     * @return void
     */
    public function headscriptAction() {

    }
    

}
