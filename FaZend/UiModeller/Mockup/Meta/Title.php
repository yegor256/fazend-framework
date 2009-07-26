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
 * Mockup meta element, page title
 *
 * @package FaZend 
 */
class FaZend_UiModeller_Mockup_Meta_Title extends FaZend_UiModeller_Mockup_Meta_Abstract {

    const FONT_SIZE = 22;

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y) {

        $this->_mockup->getImage()->imagettftext(self::FONT_SIZE, 0, FaZend_UiModeller_Mockup::INDENT, $y + self::FONT_SIZE, 
            $this->_mockup->getImage()->getColor('mockup.content.title'), 
            $this->_mockup->getImage()->getFont('mockup.content.title'), 
            $this->_parse($this->label));

        return self::FONT_SIZE * 2;

    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html() {
        return '<h1>' . nl2br($this->_parse($this->label)) . '</h1>';
    }

}
