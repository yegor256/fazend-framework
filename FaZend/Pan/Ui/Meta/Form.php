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
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_Pan_Ui_Meta_Form extends FaZend_Pan_Ui_Meta_Abstract {

    /**
     * Form is visible in TWO columns (name of field, field)
     *
     * @var boolean
     */
    protected $_alignedStyle = true;

    /**
     * Draw in PNG
     *
     * @return int Height
     */
    public function draw($y) {
        $fields = $this->_getOptions('/^field.*/');

        $height = 0;
        foreach ($fields as $field) {
            $height += $field->draw($y + $height);
        }

        return $height;
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
            $field->setAlignedStyle($this->_alignedStyle);
            $html .= $field->html();
        }

        if ($this->_alignedStyle)
            return '<table>' . $html . '</table>';
        else
            return $html;
    }

    /**
     * Set the style of form, an aligned one
     *
     * @return this
     */
    public function setAlignedStyle($style = true) {
        $this->_alignedStyle = $style;
        return $this;
    }
    
    /**
     * Add new field
     *
     * @param string Name of the field, unique in form
     * @param string Type of the field ('text', 'textarea', etc)
     * @param string Value to show in the field
     * @param string Header to show, if different from $name
     * @return this
     */
    public function addField($name, $type, $value, $header = false) {
        $type = 'FaZend_Pan_Ui_Meta_Form' . ucfirst($type);

        if (is_null($header))
            $header = $name;

        $cls = new $type($this->_mockup);
        $cls->__call('setValue', array($value));
        $cls->__call('setHeader', array($header));

        $this->__set('field' . $name, $cls);

        return $this;
    }

}
