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

/**
 * Form
 *
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_Pan_Ui_Meta_Form extends FaZend_Pan_Ui_Meta_Abstract
{

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
    public function draw($y)
    {
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
    public function html()
    {
        $fields = $this->_getOptions('/^field.*/');

        $html = '';
        foreach ($fields as $field) {
            $field->setAlignedStyle($this->_alignedStyle);
            $html .= $field->html();
        }

        if ($this->_alignedStyle)
            return "<table class='forma'>" . $html . '</table>';
        else
            return $html;
    }

    /**
     * Set the style of form, an aligned one
     *
     * @return this
     */
    public function setAlignedStyle($style = true)
    {
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
    public function addField($name, $type, $value, $header = false)
    {
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
