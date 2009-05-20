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

class FaZend_Controller_Action extends Zend_Controller_Action {

	/**
	* Get param or throw an error
	*
	* @return string
	*/
	protected function _getParam ($name) {

		if (!$this->_hasParam($name))
			throw new FaZend_Controller_Action_ParamNotFoundException("$name is not specified");

		return parent::_getParam($name);	

	}

	/**
	* Get param or return false
	*
	* @return string|false
	*/
	protected function _getParamOrFalse ($name) {

		if (!$this->_hasParam($name))
			return false;

		return parent::_getParam($name);	

	}

	/**
	* Skips this page
	*
	* @return void
	*/
	protected function _forwardWithMessage ($msg, $action = 'index', $controller = 'index') {

       		$this->view->errorMessage = $msg;
       		return $this->_forward($action, $controller); 

	}

	/**
	* Show PNG instead of page
	*
	* @return void
	*/
	protected function _returnPNG ($png) {
        
	        $this->_helper->layout->disableLayout();
        	$this->_helper->viewRenderer->setNoRender();

        	$this->getResponse()
        		->setHeader('Content-type', 'image/png')
	        	->setBody($png);

        }	
}