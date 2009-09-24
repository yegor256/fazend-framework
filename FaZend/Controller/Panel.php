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

require_once 'Zend/Controller/Action.php';
require_once 'FaZend/Controller/controllers/LoginController.php';

/**
 * Action controller for Panel
 *
 * @package Controller
 */
class FaZend_Controller_Panel extends FaZend_Controller_Action {

    /**
     * Change the layout and process authentication
     *
     * @link http://framework.zend.com/manual/en/zend.auth.adapter.http.html
     * @return void
     */
    public function preDispatch() {

        // no login in testing/development environment
        if ((APPLICATION_ENV === 'production') && !Fazend_LoginController::isLoggedIn())
            return $this->_forward('login', 'login', 'fazend');
        
        // layout reconfigure to fazend
        $layout = Zend_Layout::getMvcInstance();
        $layout->setViewScriptPath(FAZEND_PATH . '/View/layouts/scripts');
        $layout->setLayout('panel');

    }
    
}