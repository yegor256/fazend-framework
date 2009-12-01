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
class FaZend_Pan_Ui_Meta_FormSubmit extends FaZend_Pan_Ui_Meta_FormElement {

    /**
     * Draw in PNG
     *
     * @return int Height
     */
    public function draw($y) {
        $txt = $this->_parse($this->value);

        // calulate the width of the text inside the button
        list($width, ) = FaZend_Image::getTextDimensions($txt, 
            FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 
            $this->_mockup->getImage()->getFont('mockup.content'));

        // white rectangle
        $this->_mockup->getImage()->imagefilledrectangle( 
            FaZend_Pan_Ui_Mockup::INDENT, $y, 
            FaZend_Pan_Ui_Mockup::INDENT + $width, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE*2, 
            $this->_mockup->getImage()->getColor('mockup.button'));

        // border
        $this->_mockup->getImage()->imagerectangle( 
            FaZend_Pan_Ui_Mockup::INDENT, $y, 
            FaZend_Pan_Ui_Mockup::INDENT + $width, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE*2, 
            $this->_mockup->getImage()->getColor('mockup.button.border'));

        // text inside the field
        $this->_mockup->getImage()->imagettftext(FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 0, 
            FaZend_Pan_Ui_Mockup::INDENT + 3, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 1.5, 
            $this->_mockup->getImage()->getColor('mockup.input.text'), 
            $this->_mockup->getImage()->getFont('mockup.input.text'), 
            $txt);

        return FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 3;
    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html() {
        $button = $this->_htmlLink($this->header, '<span class="submit">' . $this->_parse($this->value). '</span>');

        if ($this->_alignedStyle)
            return "<tr><td></td><td>{$button}</td></tr>";
        else
            return "<p>{$button}</p>";
    }

}
