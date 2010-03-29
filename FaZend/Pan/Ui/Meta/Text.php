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
class FaZend_Pan_Ui_Meta_Text extends FaZend_Pan_Ui_Meta_Abstract
{

    const FONT_SIZE = 14;

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y)
    {
        $txt = self::parse($this->label);

        // get dimensions
        list($textWidth, ) = FaZend_Image::getTextDimensions(
            $txt,
            self::FONT_SIZE,
            $this->_mockup->getImage()->getFont('mockup.content')
        );

        $scale = $textWidth/(FaZend_Pan_Ui_Mockup::WIDTH - FaZend_Pan_Ui_Mockup::INDENT*2);
        $txt = wordwrap($txt, strlen($txt) / $scale);

        $this->_mockup->getImage()->imagettftext(
            self::FONT_SIZE, 
            0, 
            FaZend_Pan_Ui_Mockup::INDENT, 
            $y + self::FONT_SIZE, 
            $this->_mockup->getImage()->getColor('mockup.content'), 
            $this->_mockup->getImage()->getFont('mockup.content'), 
            $txt
        );

        return (substr_count($txt, "\n") + 1) * self::FONT_SIZE * 1.6 + self::FONT_SIZE;
    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html()
    {
        return '<p' . ($this->bold ? ' style="font-weight:bold;"' : false) . '>' . 
        nl2br(self::parse($this->label)) . '</p>';
    }

}
