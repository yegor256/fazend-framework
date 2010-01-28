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
 * XML RPC facade for Pan_* methods and classes
 *
 * @package Pan
 * @subpackage Rpc
 */
class Fazend_RpcController extends FaZend_Controller_Panel
{

    /**
     * Sanity check before dispatching
     *
     * @return void
     */
    public function preDispatch()
    {
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
    public function indexAction()
    {
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
