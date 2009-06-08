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
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class Fazend_JsController extends FaZend_Controller_Action {

        /**
         * Show one Java Script
         * 
         * @return string
         */
        public function indexAction() {

        	// if it's absent
        	if (!file_exists(APPLICATION_PATH . '/views/scripts/js/' . $this->_getParam('script')))
        		$this->_forwardWithMessage('path not found');

        	$this->getResponse()
        		->setHeader('Content-type', 'text/javascript');

        	// tell browser to cache this content	
        	$this->_cacheContent();	

		$this->_helper->viewRenderer
			->setViewScriptPathSpec(':controller/'.$this->_getParam('script'));
	        
	        $this->_helper->layout->disableLayout();

	        $this->view->setFilter(null);

		$this->_helper->viewRenderer($this->_getParam('script'));


	}	
}

