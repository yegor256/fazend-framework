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
 * Dynamic DIV object on the page, downloadable automatically or by a user click
 *
 * @package FaZend 
 */
class FaZend_View_Helper_AjaxCall extends FaZend_View_Helper {

    /**
     * Url to call
     *
     * @var string
     */
    protected $_url = null;
    
    /**
     * Tag
     *
     * @var string
     */
    protected $_tag = 'div';
    
    /**
     * Title
     *
     * @var string
     */
    protected $_title = 'click to load';
    
    /**
     * Message to show
     *
     * @var string
     */
    protected $_message = '...';
    
    /**
     * Execute now?
     *
     * @var boolean
     */
    protected $_immediateExecution = false;
    
    /**
     * Show some text and replace it with the result of ajax call
     *
     * @return void
     */
    public function ajaxCall() {

        return $this;
    }

    /**
     * Show DIV/SPAN
     *
     * @return void
     * @throws FaZend_View_Helper_AjaxCall_URLNotDefined
     */
    public function __toString() {
        
        if (!$this->_url)
            FaZend_Exception::raise('FaZend_View_Helper_AjaxCall_URLNotDefined');

        // prototype is required for this    
        $this->getView()->headScript()->appendFile($this->getView()->url(array('script'=>'prototype.js'), 'js', true));

        // ajas function for loading content
        $this->getView()->headScript()->appendFile($this->getView()->url(array('script'=>'ajaxCall.js'), 'js', true));

        $id = 'ajax' . (microtime(true) * 10000);

        if ($this->_immediateExecution) {
            // call right now
            $this->getView()->headScript()->appendScript("ajaxCall('{$id}', '{$this->_url}');");
        } else {
            // call on the click
            $this->getView()->headScript()->appendScript(
                "$('{$id}').onclick = function() { ajaxCall('{$id}', '{$this->_url}'); };");
        }

        // clear the URL to avoid double execution
        $this->_url = null;

        return "<{$this->_tag} id='{$id}' title='{$this->_title}'" . 
            ($this->_immediateExecution ? false : " style='cursor:pointer;'"). ">{$this->_message}</{$this->_tag}>";

    }

    /**
     * Set tag
     *
     * @return $this
     */
    public function setTag($tag) {

        $this->_tag = $tag;
        return $this;
    }

    /**
     * Set title
     *
     * @return $this
     */
    public function setTitle($title) {

        $this->_title = $title;
        return $this;
    }

    /**
     * Set url
     *
     * @return $this
     */
    public function setUrl($url) {

        $this->_url = $url;
        return $this;
    }

    /**
     * Set message
     *
     * @return $this
     */
    public function setMessage($message) {

        $this->_message = $message;
        return $this;
    }

    /**
     * Should execute right now?
     *
     * @return $this
     */
    public function setImmediateExecution($flag = true) {

        $this->_immediateExecution = $flag;
        return $this;
    }

}
