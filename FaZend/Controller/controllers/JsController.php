<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoParck Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2008
 * @version $Id$
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

        	$this->getResponse()->setHeader('Content-type', 'text/javascript');

		$this->_helper->viewRenderer
			->setViewScriptPathSpec(':controller/'.$this->_getParam('script'));
	        
	        $this->_helper->layout->disableLayout();

	        $this->view->setFilter(null);

		$this->_helper->viewRenderer($this->_getParam('script'));


	}	
}

