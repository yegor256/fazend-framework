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
 * ErrorController
 */ 
class ErrorController extends Zend_Controller_Action 
{ 
    /**
     * errorAction() is the action that will be called by the "ErrorHandler" 
     * plugin.  When an error/exception has been encountered
     * in a ZF MVC application (assuming the ErrorHandler has not been disabled
     * in your bootstrap) - the Errorhandler will set the next dispatchable 
     * action to come here.  This is the "default" module, "error" controller, 
     * specifically, the "error" action.  These options are configurable. 
     * 
     * @see http://framework.zend.com/manual/en/zend.controller.plugins.html
     *
     * @return void
     */
    public function errorAction() 
    { 
        // Ensure the default view suffix is used so we always return good 
        // content
        $this->_helper->viewRenderer->setViewSuffix('phtml');

        // Grab the error object from the request
        $errors = $this->_getParam('error_handler'); 

        // $errors will be an object set as a parameter of the request object, 
        // type is a property
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 

                // 404 error -- controller or action not found 
                $this->getResponse()->setHttpResponseCode(404); 
                //$this->view->message = 'Page not found'; 

                $this->_forward('index', 'index');
                return;

                break; 

            default: 
                // application error 
                $this->getResponse()->setHttpResponseCode(500); 
                $this->view->message = 'Internal application error #'.rand (100, 999); 
                break; 
        } 

        // pass the actual exception object to the view
        $this->view->exception = $errors->exception; 
        
        // pass the request to the view
        $this->view->request = $errors->request; 

	$this->view->showError = Zend_Registry::getInstance()->configuration->errors->display;

        if (Zend_Registry::getInstance()->configuration->errors->email) {
        	$lines = array ();
        	foreach (debug_backtrace () as $line) 
        		$lines[] = "{$line['file']} ({$line['line']})";

        	mail (Zend_Registry::getInstance()->configuration->errors->email, WEBSITE_URL.' internal PHP error, rev.'.Model_Revision::get().': '.$_SERVER['REQUEST_URI'], 
	        	$errors->exception->getMessage()."\n\n".
	        	implode("\n", $lines)."\n\n".
	        	print_r($errors->request->getParams(), true)."\n\n".
	        	$errors->exception->getTraceAsString());
	}	
    } 
}
