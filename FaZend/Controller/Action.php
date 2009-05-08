<?php
/**
 *
 * Copyright (c) 2009, FaZend.com
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of FaZend.com. located at
 * www.FaZend.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@FaZend.com
 *
 * @copyright Copyright (c) FaZend.com, 2009
 * @version $Id$
 *
 * Using the information from /scripts directory deploys changes to the DB
 *
 */

class FaZend_Controller_Action extends Zend_Controller_Action {

	/**
	* Skips this page
	*
	* @return void
	*/
	protected function _forwardWithMessage ($msg, $action = 'index', $controller = 'index') {

       		$this->view->errorMessage = $msg;
       		$this->_forward($action, $controller); 
       		return;

	}

	/**
	* Show PNG instead of page
	*
	* @return void
	*/
	protected function _returnPNG ($png) {
        
	        $this->_helper->layout->disableLayout();
        	$this->_helper->viewRenderer->setNoRender();

        	header('Content-type: image/png');
        	echo $png;
        	die();

        }	
}