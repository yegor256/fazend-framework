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

require_once 'FaZend/View/Helper.php';

/**
 * Dynamic DIV object on the page, downloadable automatically or by a user click
 *
 * @package View
 * @subpackage Helper
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

        // jQuery is required for this    
        $this->getView()->includeJQuery();

        // ajas function for loading content
        $this->getView()->headScript()->appendFile($this->getView()->url(array('script'=>'ajaxCall.js'), 'js', true));

        $id = 'ajax' . (microtime(true) * 10000);

        if ($this->_immediateExecution) {
            // call right now
            $this->getView()->headScript()->appendScript("ajaxCall($('#{$id}'), '{$this->_url}');");
        } else {
            // call on the click
            $this->getView()->headScript()->appendScript(
                "$('#{$id}').bind('click', function() { ajaxCall($('#{$id}'), '{$this->_url}'); });");
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
