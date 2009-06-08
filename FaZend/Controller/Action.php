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

       		return $this->_forward($action, $controller, 'default', array('error'=>$msg)); 

	}

	/**
	* Show PNG instead of page
	*
	* You have to remember, that under SSL all images are dynamic, no matter
	* what parameter you set here. And you can't change this.
	*
	* @param string PNG binary content
	* @param boolean This image is dynamic (TRUE) or static (FALSE).
	* @return void
	*/
	protected function _returnPNG ($png, $dynamic = true) {
        
	        $this->_helper->layout->disableLayout();
        	$this->_helper->viewRenderer->setNoRender();

        	// if the image is static - tell the browser about it
        	if (!$dynamic) {
	        	// when it was created	
        		$this->getResponse()->setHeader('Last-Modified', gmdate('D, d M Y H:i:s', time()) . ' GMT')

        		// in 30 days to reload!
	        	->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 60 * 60 * 24 * 30) . ' GMT')

	        	// tell the browser NOT to reload the image
        		->setHeader('Cache-Control', 'public')
        		->setHeader('Pragma', '');
        	}

        	$this->getResponse()
        		->setHeader('Content-type', 'image/png')
	        	->setBody($png);

        }	

	/**
	* Return JSON reply
	*
	* @return void
	*/
	protected function _returnJSON ($var) {
        
	        $this->_helper->layout->disableLayout();
        	$this->_helper->viewRenderer->setNoRender();

		try {
     
			$responseJsonEncoded = Zend_Json::encode($var);
			$this->getResponse()
				->setHeader('Content-Type', 'application/json')
				->setBody($responseJsonEncoded);

		} catch(Zend_Json_Exception $e) {

		}

	}	

	/**
	 * Return XML reply
	 *
	 * @return void
	 */
	protected function _returnXML ($xml) {
        
	        $this->_helper->layout->disableLayout();
        	$this->_helper->viewRenderer->setNoRender();

		$this->getResponse()
			->setHeader('Content-Type', 'text/xml')
			->setBody($xml);

	}

}