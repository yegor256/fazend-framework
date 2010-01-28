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
 * Form textarea
 *
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_Pan_Ui_Meta_FormTextarea extends FaZend_Pan_Ui_Meta_FormElement
{

    const WIDTH = 25;
    const HEIGHT = 4;

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y)
    {
        $width = FaZend_Pan_Ui_Meta_Text::FONT_SIZE * self::WIDTH;
        $height = FaZend_Pan_Ui_Meta_Text::FONT_SIZE * self::HEIGHT;

        // element header
        $this->_mockup->getImage()->imagettftext(
            FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 
            0, 
            FaZend_Pan_Ui_Mockup::INDENT, 
            $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 
            $this->_mockup->getImage()->getColor('mockup.content'), 
            $this->_mockup->getImage()->getFont('mockup.content'), 
            $this->_parse($this->header) . ':'
        );

        $y += FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 2;

        // white rectangle
        $this->_mockup->getImage()->imagefilledrectangle(
            FaZend_Pan_Ui_Mockup::INDENT, 
            $y, 
            FaZend_Pan_Ui_Mockup::INDENT + $width, 
            $y + $height, 
            $this->_mockup->getImage()->getColor('mockup.input')
        );

        // border
        $this->_mockup->getImage()->imagerectangle(
            FaZend_Pan_Ui_Mockup::INDENT, 
            $y, 
            FaZend_Pan_Ui_Mockup::INDENT + $width, 
            $y + $height, 
            $this->_mockup->getImage()->getColor('mockup.input.border')
        );

        // text inside the field
        $this->_mockup->getImage()->imagettftext(
            FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 
            0, 
            FaZend_Pan_Ui_Mockup::INDENT + 3, 
            $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 1.5, 
            $this->_mockup->getImage()->getColor('mockup.input.text'), 
            $this->_mockup->getImage()->getFont('mockup.input.text'), 
            $this->_parse($this->value)
        );

        return $height + FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 3.5;
    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html()
    {
        $header = $this->_parse($this->header);
        $input = '<textarea cols="' . self::WIDTH . '" rows="' . self::HEIGHT . '">' . 
        $this->_parse($this->value) . '</textarea>';

        if ($this->_alignedStyle)
            return "<tr><td class='left'>{$header}:</td><td>{$input}</td></tr>";
        else
            return "<p>{$header}:<br/>{$input}</p>";
    }

}
