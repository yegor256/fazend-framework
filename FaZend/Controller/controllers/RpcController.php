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
 * XML RPC facade for Pan_* methods and classes
 *
 * @package Pan
 * @subpackage Rpc
 */
class Fazend_RpcController extends FaZend_Controller_Panel {

    /**
     * Sanity check before dispatching
     *
     * @return void
     */
    public function preDispatch() {
        // sanity check
        if (APPLICATION_ENV == 'production')
            $this->_redirectFlash('XML RPC controller is not allowed in production environment', 'restrict', 'login');
        
        parent::preDispatch();
    }

    /**
     * Show the entire map of the system
     *
     * @return void
     */
    public function indexAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $server = new Zend_XmlRpc_Server();
        $server->setClass('FaZend_Pan_Database_Facade', 'database');
        $server->setClass('FaZend_Pan_Ui_Facade', 'ui');
        $server->setClass('FaZend_Pan_Analysis_Facade', 'analysis');
        $server->setClass('FaZend_Pan_Tests_Facade', 'tests');
        echo $server->handle();
    }
        
}
