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

require_once 'Zend/View/Helper/FormText.php';

/**
 * Text field with an Ajax drop down list of possible values
 *
 * @package View
 * @subpackage Helper
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
        $url = $this->view->url(array(
            'action'=>$this->_getDefault($opts, 'list/action', 'list'),
            'controller'=>$this->_getDefault($opts, 'list/controller', 'index')), $this->_getDefault($opts, 'list/route', 'default'), true);

        // url for ajax check call
        $handUrl = $this->view->url(array(
            'action'=>$this->_getDefault($opts, 'hand/action', 'list'),
            'controller'=>$this->_getDefault($opts, 'hand/controller', 'index')), $this->_getDefault($opts, 'hand/route', 'default'), true);

        // ajaz functions
        $this->view->headScript()->appendFile($this->view->url(array('script'=>'formAjaxText.js'), 'js', true));

        // jQuery is required for this    
        $this->view->includeJQuery();

        // initialize JS handlers
        $callback = "function() {ajax_UpdateList($('#{$id}'), $('#{$listId}'), '{$url}', $('#".$this->_getDefault($opts, 'next')."'), $('#{$handId}'), '{$handUrl}');}";
        $this->view->headScript()->appendScript(
            "$('#{$id}').bind('keyup', {$callback});\n" .
            "$('#{$id}').bind('focus', {$callback});\n" .
            "$('#{$id}').bind('blur', function() {ajax_LostFocus($('#{$listId}'));});\n");

        // disable autocomplete in most browsers
        $attribs['autocomplete'] = 'off';
        
        return parent::formText($name, $value, $attribs) . 
        
        "<div id='" . $this->view->escape($handId) . "'" .
            (isset($opts['hand']['class']) ? " class='{$opts['hand']['class']}'" : false). "></div>" . 

        "<div id='" . $this->view->escape($listId) . "'" . 
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
