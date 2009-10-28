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
 * - header: title to show
 * - value: list of values into the list
 *
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_Pan_Ui_Meta_FormSelect extends FaZend_Pan_Ui_Meta_FormElement {

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y) {

        $txt = $this->_parse($this->value);

        $width = FaZend_Pan_Ui_Meta_Text::FONT_SIZE * min(25, strlen($txt) + 3);

        // element header
        $this->_mockup->getImage()->imagettftext(FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 0, 
            FaZend_Pan_Ui_Mockup::INDENT, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 
            $this->_mockup->getImage()->getColor('mockup.content'), 
            $this->_mockup->getImage()->getFont('mockup.content'), 
            $this->_parse($this->header) . ':');

        $y += FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 2;

        // white rectangle
        $this->_mockup->getImage()->imagefilledrectangle( 
            FaZend_Pan_Ui_Mockup::INDENT, $y, 
            FaZend_Pan_Ui_Mockup::INDENT + $width, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE*2, 
            $this->_mockup->getImage()->getColor('mockup.input'));

        // border
        $this->_mockup->getImage()->imagerectangle( 
            FaZend_Pan_Ui_Mockup::INDENT, $y, 
            FaZend_Pan_Ui_Mockup::INDENT + $width, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE*2, 
            $this->_mockup->getImage()->getColor('mockup.input.border'));

        // triangle
        $this->_mockup->getImage()->imagefilledpolygon( 
            array(
                FaZend_Pan_Ui_Mockup::INDENT + $width - 16, $y + 8, 
                FaZend_Pan_Ui_Mockup::INDENT + $width - 4, $y + 8,
                FaZend_Pan_Ui_Mockup::INDENT + $width - 10, $y + 18), 
            3, $this->_mockup->getImage()->getColor('mockup.input.border'));

        // text inside the field
        $this->_mockup->getImage()->imagettftext(FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 0, 
            FaZend_Pan_Ui_Mockup::INDENT + 3, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 1.5, 
            $this->_mockup->getImage()->getColor('mockup.input.text'), 
            $this->_mockup->getImage()->getFont('mockup.input.text'), 
            $txt);

        return FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 5;

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
