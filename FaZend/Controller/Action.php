<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
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
class FaZend_Controller_Action extends Zend_Controller_Action
{

    /**
     * Format of Date for HTTP: "Tue, 15 Nov 1994 08:12:31 GMT"
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
     */
    const HTTP_DATE = 'WWW, dd MMM y HH:mm:ss Z';

    /**
     * Add new paginator to the view
     *
     * @param ArrayIterator
     * @param string Name of the variable inside $this->view to be set
     * @param string name of the param for paging
     * @return string
     */
    protected function _addPaginator($iterator, $name = 'paginator', $param = 'page')
    {
        FaZend_Paginator::addPaginator(
            $iterator, 
            $this->view, 
            $this->_getParamOrFalse($param), 
            $name
        );
    }

    /**
     * Get param or throw an error
     *
     * @param string Name of param to get
     * @return string
     * @throws FaZend_Controller_Action_ParamNotFoundException
     */
    protected function _getParam($name, $default = null)
    {
        if (!$this->_hasParam($name) && is_null($default)) {
            FaZend_Exception::raise(
                'FaZend_Controller_Action_ParamNotFoundException', 
                "Parameter '{$name}' is not specified"
            );
        }
        return parent::_getParam($name, $default);    
    }

    /**
     * Get param or return false
     *
     * @return string|false
     */
    protected function _getParamOrFalse($name)
    {
        if (!$this->_hasParam($name)) {
            return false;
        }
        return parent::_getParam($name);    
    }

    /**
     * Redirect to url with flash message
     *
     * Forwards current dispatching cycle to the new location, adding
     * flash message to the session.
     *
     * @param string message Flash message
     * @param string|false Action name (FALSE = don't do redirect, just save FLASH message to Session)
     * @param string|null Controller name
     * @param string|null Module name
     * @param array List of parameters (associative array)
     * @return void
     */    
    protected function _redirectFlash(
        $message, 
        $action = 'index', 
        $controller = null, 
        $module = null, 
        array $params = array()
    )
    {
        $this->_helper->flashMessenger->setNamespace('FaZend_Messages')->addMessage($message);        
        if ($action !== false) {
            $this->_helper->redirector->gotoSimple($action, $controller, $module, $params);
        }
    }

    /**
     * Return content in any MIME-type.
     *
     * @param string Content
     * @param boolean Age of content to specify (for cache), NULL means "no cache"
     * @return void
     */
    protected function _return($type, $body, $age = null)
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // if (!is_null($age)) {
        //     $now = Zend_Date::now();
        //     $this->getResponse()
        //         ->setHeader('Last-Modified', $now->get(self::HTTP_DATE))
        //         ->setHeader('Date', $now->get(self::HTTP_DATE))
        //         ->setHeader('Pragma', '')
        //         ->setHeader('Expires', $now->add($age, Zend_Date::SECOND)->get(self::HTTP_DATE))
        //         ->setHeader('Cache-Control', 'public, max-age=' . $age);
        // }
        
        $this->getResponse()
            ->setHeader('Content-Type', $type)
            ->setHeader('Content-Length', strlen($body))
            ->setBody($body);
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
    protected function _returnPNG($png, $dynamic = true)
    {
        return $this->_return('image/png', $png, $dynamic ? null : 3600);
    }    

    /**
     * Show PDF instead of page
     *
     * @param string PDF binary content
     * @return void
     */
    protected function _returnPDF($pdf)
    {
        return $this->_return('application/pdf', $pdf);
    }    

    /**
     * Return JSON reply
     *
     * @return void
     */
    protected function _returnJSON($var)
    {
        return $this->_return('application/json', Zend_Json::encode($var));
    }    

    /**
     * Return XML reply
     *
     * @return void
     */
    protected function _returnXML($xml)
    {
        return $this->_return('application/xml', Zend_Json::encode($xml));
    }

}