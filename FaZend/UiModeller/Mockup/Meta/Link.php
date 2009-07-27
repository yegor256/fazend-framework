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

        $txt = $this->_parse($this->label);

        $bbox = imagettfbbox(FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE, 0, 
            $this->_mockup->getImage()->getFont('mockup.content'), $txt);

        $this->_mockup->getImage()->imagettftext(FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE, 0, 
            FaZend_UiModeller_Mockup::INDENT, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE, 
            $this->_mockup->getImage()->getColor('mockup.link'), 
            $this->_mockup->getImage()->getFont('mockup.content'), 
            $txt);

        $this->_mockup->getImage()->imageline(
            FaZend_UiModeller_Mockup::INDENT, $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE + 1, 
            FaZend_UiModeller_Mockup::INDENT + $bbox[4], $y + FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE + 1, 
            $this->_mockup->getImage()->getColor('mockup.link'));

        return FaZend_UiModeller_Mockup_Meta_Text::FONT_SIZE * 1.6;

    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html() {
        return '<p>' . $this->_htmlLink($this->destination, $this->_parse($this->label)) . '</p>';
    }

}
