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
 * Mockup meta element, text
 *
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_Pan_Ui_Meta_Link extends FaZend_Pan_Ui_Meta_Abstract
{

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y)
    {
        $txt = self::parse($this->label);

        list($textWidth, ) = FaZend_Image::getTextDimensions(
            $txt,
            FaZend_Pan_Ui_Meta_Text::FONT_SIZE,
            $this->_mockup->getImage()->getFont('mockup.content')
        );

        $this->_mockup->getImage()->imagettftext(
            FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 0, 
            FaZend_Pan_Ui_Mockup::INDENT, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 
            $this->_mockup->getImage()->getColor('mockup.link'), 
            $this->_mockup->getImage()->getFont('mockup.content'), 
            $txt
        );

        $this->_mockup->getImage()->imageline(
            FaZend_Pan_Ui_Mockup::INDENT, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE + 1, 
            FaZend_Pan_Ui_Mockup::INDENT + $textWidth, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE + 1, 
            $this->_mockup->getImage()->getColor('mockup.link')
        );

        return FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 2;
    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html()
    {
        return '<p>' . $this->_htmlLink($this->destination, self::parse($this->label)) . '</p>';
    }

}
