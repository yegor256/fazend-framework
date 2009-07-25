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
 * Form select field
 *
 * @package FaZend 
 */
class FaZend_UiModeller_Mockup_Meta_FormSelect extends FaZend_UiModeller_Mockup_Meta_FormElement {

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
     * @return string HTML image of the element
     */
    public function html() {

        $html = '<p>' . $this->_parse($this->header) . ':<br/>' . 
            '<select>';

        foreach ($this->value as $option)
            $html .= '<option>' . $this->_parse($option) . '</option>';
            
        $html .= '</select></p>';

        return $html;
    }

}
