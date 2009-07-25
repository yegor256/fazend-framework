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
 * Form
 *
 * @package FaZend 
 */
class FaZend_UiModeller_Mockup_Meta_Form extends FaZend_UiModeller_Mockup_Meta_Abstract {

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y) {

        $fields = $this->_getOptions('/^field.*/');

        foreach ($fields as $field) {
            $y += $field->draw($y);
        }

        return $y;

    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html() {

        $fields = $this->_getOptions('/^field.*/');

        $html = '';

        foreach ($fields as $field) {
            $html .= $field->html();
        }

        return $html;

    }

    /**
     * Add new field
     *
     * @return this
     */
    public function addField($name, $type, $value, $header) {

        $type = 'FaZend_UiModeller_Mockup_Meta_Form' . ucfirst($type);

        $cls = new $type($this->_mockup);
        $cls->__call('setValue', array($value));
        $cls->__call('setHeader', array($header));

        $this->__set('field' . $name, $cls);

        return $this;

    }

}
