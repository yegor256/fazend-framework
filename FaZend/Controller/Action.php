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
 * Action controller
 *
 * @package Controller
 */
class FaZend_Controller_Action extends Zend_Controller_Action {

    /**
     * Add new paginator to the view
     *
     * @param ArrayIterator
     * @param string Name of the variable inside $this->view to be set
     * @param string name of the param for paging
     * @return string
     */
    protected function _addPaginator ($iterator, $name = 'paginator', $param = 'page') {
        FaZend_Paginator::addPaginator($iterator, $this->view, $this->_getParamOrFalse($param), $name);
    }

    /**
     * Get param or throw an error
     *
     * @return string
     */
    protected function _getParam ($name, $default = null) {
        if (!$this->_hasParam($name))
            FaZend_Exception::raise('FaZend_Controller_Action_ParamNotFoundException', "$name is not specified");

        return parent::_getParam($name, $default);    
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
     * Redirect to url with flash message
     *
     * Forwards current dispatching cycle to the new location, adding
     * flash message to the session.
     *
     * @param string message Flash message
     * @param string Action name
     * @param string|null Controller name
     * @param string|null Module name
     * @param array List of parameters (associative array)
     * @return void
     */    
    protected function _redirectFlash($message, $action = 'index', $controller = null, $module = null, array $params = array()) {
        $this->_helper->flashMessenger->setNamespace('FaZend_Messages')->addMessage($message);        
        $this->_helper->redirector->gotoSimple($action, $controller, $module, $params);
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
    protected function _returnPNG($png, $dynamic = true) {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // if the image is static - tell the browser about it
        if (!$dynamic)
            $this->_cacheContent();

        $this->getResponse()
            ->setHeader('Content-Type', 'image/png')
            ->setHeader('Content-Length', strlen($png))
            ->setBody($png);
    }    

    /**
     * Show PDF instead of page
     *
     * @param string PDF binary content
     * @return void
     */
    protected function _returnPDF($pdf) {
    
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $this->getResponse()
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Length', strlen($pdf))
            ->setBody($pdf);

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
                ->setHeader('Content-Length', strlen($responseJsonEncoded))
                ->setBody($responseJsonEncoded);

        } catch (Zend_Json_Exception $e) {

            // what to do here?

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

    /**
     * Format time for HTTP headers
     *
     * @param int
     * @return string
     */
    protected function _formatHeaderTime($time) {
        return gmdate('D, d M Y H:i:s', $time) . ' GMT';
    }
    
    /**
     * Tell browser to cache content
     *
     * @param int Time when this content was modified last time
     * @return void
     */
    protected function _cacheContent($modifiedTime = false) {

        if (!$modifiedTime)
            $modifiedTime = time();
    
        $this->getResponse()
            // when this images was last modified
            ->setHeader('Last-Modified', $this->_formatHeaderTime($modifiedTime))
    
            ->setHeader('Date', $this->_formatHeaderTime(time()))

            ->setHeader('Pragma', '')
            
            // in 30 days to reload!
            ->setHeader('Expires', $this->_formatHeaderTime($modifiedTime + 60 * 60 * 24 * 30))
            
            // tell the browser NOT to reload the image
            ->setHeader('Cache-Control', 'public, max-age=31536000');
            
            //->setHeader('Content-Encoding', 'gzip, deflate')
            //->setHeader('X-Compression', 'gzip')
            //->setHeader('Accept-Encoding', 'gzip');
    
    }    

}