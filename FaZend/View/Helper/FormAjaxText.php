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
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_FormAjaxText extends Zend_View_Helper_FormText {

        /**
         * @param mixed $value The element value.
         * @param array $attribs Attributes for the element tag.
         * @return string The element XHTML.
         */
	public function formAjaxText($name, $value = null, $attribs = null) {

        	$info = $this->_getInfo($name, $value, $attribs);
	        extract($info); // name, value, attribs, options, listsep, disable

	        // DIV behind the text field
	        $listId = $id . '_list';

	        // DIV right of the text field
	        $handId = $id . '_hand';

	        $opts = $attribs['ajax'];
	        unset($attribs['ajax']);

	        // url for ajax call
	        $url = $this->getView()->url(array(
	        	'action'=>$this->_getDefault($opts, 'list/action', 'list'),
	        	'controller'=>$this->_getDefault($opts, 'list/controller', 'index')), $this->_getDefault($opts, 'list/route', 'default'), true);

	        // url for ajax check call
	        $handUrl = $this->getView()->url(array(
	        	'action'=>$this->_getDefault($opts, 'hand/action', 'list'),
	        	'controller'=>$this->_getDefault($opts, 'hand/controller', 'index')), $this->_getDefault($opts, 'hand/route', 'default'), true);

		// ajaz functions
	        $this->getView()->headScript()->appendFile($this->getView()->url(array('script'=>'formAjaxText.js'), 'js', true));

	        // prototype is required for this	
	        $this->getView()->headScript()->appendFile($this->getView()->url(array('script'=>'prototype.js'), 'js', true));

		// initialize JS handlers
		$callback = "function() {ajax_UpdateList('{$id}', '{$listId}', '{$url}', '".$this->_getDefault($opts, 'next')."', '{$handId}', '{$handUrl}');}";
	        $this->getView()->headScript()->appendScript(
	        	"var div_{$id} = document.getElementById('{$id}');\n" .
	        	"div_{$id}.onkeyup = {$callback};\n" .
	        	"div_{$id}.onfocus = {$callback};\n" .
	        	"div_{$id}.onblur = function() {ajax_LostFocus('{$listId}');};\n");

	        // disable autocomplete in most browsers
		$attribs['autocomplete'] = 'off';
		
		return parent::formText($name, $value, $attribs) . 
		
		"<div id='" . $this->getView()->escape($handId) . "'" .
			(isset($opts['hand']['class']) ? " class='{$opts['hand']['class']}'" : false). "></div>" . 

		"<div id='" . $this->getView()->escape($listId) . "'" . 
			(isset($opts['list']['class']) ? " class='{$opts['list']['class']}'" : false). "></div>";


	}

        /**
         * Get default value or real
         *
         * @return void
         */
	private function _getDefault($opts, $key, $default = false) {

		if (strpos($key, '/')) {
			$exp = explode('/', $key);
			if (!isset($opts[$exp[0]]))
				return $default;
			$opts = $opts[$exp[0]];	
			$key = $exp[1];
		}

		if (isset($opts[$key]))
			return $opts[$key];

		return $default;	

	}

}
