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
 * Index controller
 *
 * Dispatcher of all pages/actions
 *
 * @package application
 * @subpackage controllers
 * @see Bootstrap
 */
class IndexController extends FaZend_Controller_Action
{

    /**
     * Test _redirectFlash() method
     *
     * @return void
     * @see IndexController
     */
    public function flashAction()
    {
        $this->_redirectFlash('That works');
    }    

    /**
     * Test htmlTable helper
     *
     * @return void
     * @see IndexController
     */
    public function tableAction()
    {
        FaZend_Paginator::addPaginator(Model_Owner::retrieveAll(), $this->view, 0);
    }
    
    /**
     * Test forma() helper.
     *
     * @return void
     * @see IndexController
     */
    public function formaAction()
    {
    }
    
}
