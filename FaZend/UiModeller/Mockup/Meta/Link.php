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
 * Mockup meta element, text
 *
 * @package FaZend 
 */
class FaZend_UiModeller_Mockup_Meta_Link extends FaZend_UiModeller_Mockup_Meta_Abstract {

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y) {


    }

    /**
     * Convert to HTML
     *
     * @param Zend_View Current view
     * @return string HTML image of the element
     */
    public function html(Zend_View $view) {
        return '<p><a href="' . $view->url(array('action'=>'index', 'id'=>$this->destination), 'ui', true, false). '">' . 
            $this->_parse($this->label). '</a></p>';
    }

}
