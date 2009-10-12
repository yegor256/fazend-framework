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
 * Form text
 *
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_UiModeller_Mockup_Meta_FormText extends FaZend_UiModeller_Mockup_Meta_FormElement {

    const WIDTH = 25; // maximum width

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y) {

        $txt = $this->_parse($this->value);
        $width = FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE * min(self::WIDTH, strlen($txt) + 2);

        // element header
        $this->_mockup->getImage()->imagettftext(FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE, 0, 
            FaZend_UiModeller_Mockup::INDENT, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE, 
            $this->_mockup->getImage()->getColor('mockup.content'), 
            $this->_mockup->getImage()->getFont('mockup.content'), 
            $this->_parse($this->header) . ':');

        $y += FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE * 2;

        // white rectangle
        $this->_mockup->getImage()->imagefilledrectangle( 
            FaZend_UiModeller_Mockup::INDENT, $y, 
            FaZend_UiModeller_Mockup::INDENT + $width, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE*2, 
            $this->_mockup->getImage()->getColor('mockup.input'));

        // border
        $this->_mockup->getImage()->imagerectangle( 
            FaZend_UiModeller_Mockup::INDENT, $y, 
            FaZend_UiModeller_Mockup::INDENT + $width, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE*2, 
            $this->_mockup->getImage()->getColor('mockup.input.border'));

        // text inside the field
        $this->_mockup->getImage()->imagettftext(FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE, 0, 
            FaZend_UiModeller_Mockup::INDENT + 3, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE * 1.5, 
            $this->_mockup->getImage()->getColor('mockup.input.text'), 
            $this->_mockup->getImage()->getFont('mockup.input.text'), 
            $txt);

        return FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE * 5;

    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html() {

        $txt = $this->_parse($this->value);

        $html = '<p>' . $this->_parse($this->header) . ':<br/>' .
            '<input type="text" value="' . $txt . '" size=" ' . 
            min(self::WIDTH, strlen($txt)) . '"/></p>';

        return $html;
    }

}
