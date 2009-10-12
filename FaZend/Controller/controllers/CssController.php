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
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 * @package controllers
 */
class Fazend_CssController extends FaZend_Controller_Action {

    /**
     * Show one Java Script
     * 
     * @return string
     */
    public function indexAction() {

        // if it's absent
        //if (!file_exists(APPLICATION_PATH . '/views/scripts/css/' . $this->_getParam('css')))
        //    $this->_redirectFlash('path not found');

        $this->getResponse()
            ->setHeader('Content-type', 'text/css');

        $this->_cacheContent();

        $this->_helper->viewRenderer
            ->setViewScriptPathSpec(':controller/'.$this->_getParam('css'));
        
        $this->_helper->layout->disableLayout();

        $this->view->setFilter(null);

        if (FaZend_Properties::get()->htmlCompression)
            $this->view->addFilter('CssCompressor');

        $this->_helper->viewRenderer($this->_getParam('css'));


    }    
}

