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
 * @package FaZend 
 */
class FaZend_UiModeller_Mockup_Meta_FormSubmit extends FaZend_UiModeller_Mockup_Meta_FormElement {

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y) {

        $txt = $this->_parse($this->value);
        $bbox = imagettfbbox(FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE, 0, $this->_mockup->getImage()->getFont('mockup.content'), $txt);

        $width = $bbox[4] + 10;

        // white rectangle
        $this->_mockup->getImage()->imagefilledrectangle( 
            FaZend_UiModeller_Mockup::INDENT, $y, 
            FaZend_UiModeller_Mockup::INDENT + $width, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE*2, 
            $this->_mockup->getImage()->getColor('mockup.button'));

        // border
        $this->_mockup->getImage()->imagerectangle( 
            FaZend_UiModeller_Mockup::INDENT, $y, 
            FaZend_UiModeller_Mockup::INDENT + $width, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE*2, 
            $this->_mockup->getImage()->getColor('mockup.button.border'));

        // text inside the field
        $this->_mockup->getImage()->imagettftext(FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE, 0, 
            FaZend_UiModeller_Mockup::INDENT + 3, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE * 1.5, 
            $this->_mockup->getImage()->getColor('mockup.input.text'), 
            $this->_mockup->getImage()->getFont('mockup.input.text'), 
            $txt);

        return FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE * 3;

    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html() {

        return '<p>' . $this->_htmlLink($this->header, '<span class="submit">' . 
            $this->_parse($this->value). '</span>') . '</p>';

    }

}
