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
 * Static file delivery from "views/files"
 *
 * @package controllers
 */
class Fazend_FileController extends FaZend_Controller_Action {

    /**
     * Show one file
     * 
     * @return void
     */
    public function indexAction() {
        //$this->getResponse()
        //    ->setHeader('Content-type', 'text/javascript');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $file = APPLICATION_PATH . '/views/files/' . $this->_getParam('file');

        // if it's absent
        if (!file_exists($file)) {
            $file = FAZEND_PATH . '/View/files/' . $this->_getParam('file');
            if (!file_exists($file))
                $this->getResponse()->setBody('file ' . $this->_getParam('file') . ' not found');
                return;
        }

        // tell browser to cache this content    
        $this->_cacheContent();    

        $this->getResponse()->setBody(file_get_contents($file));
    }    
    
}

