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
        
        // layout reconfigure to fazend
        $layout = Zend_Layout::getMvcInstance();
        $layout->setViewScriptPath(FAZEND_PATH . '/View/layouts/scripts');
        $layout->setLayout('panel');

        // no login in testing/development environment
        if (APPLICATION_ENV !== 'production')
            return;

        $resolver = new FaZend_Auth_Adapter_Http_Resolver_Admins();
        $resolver->setScheme('basic');    

        // all this will work ONLY if PHP is installed as Apache Module
        // @see: http://www.php.net/features.http-auth
        if (FaZend_User::isLoggedIn() && $resolver->resolve(FaZend_User::getCurrentUser()->email, 'adm'))
            return;

        $adapter = new Zend_Auth_Adapter_Http(array(
            'accept_schemes' => 'basic',
            'realm' => 'adm'));

        $adapter->setBasicResolver($resolver);
        $adapter->setRequest($this->getRequest());
        $adapter->setResponse($this->getResponse());

        $result = $adapter->authenticate();
        if (!$result->isValid()) {
            return $this->_forward('index', 'index');
        }    

    }


}