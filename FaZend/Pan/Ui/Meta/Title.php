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
 * Mockup meta element, page title
 *
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_Pan_Ui_Meta_Title extends FaZend_Pan_Ui_Meta_Abstract
{

    const FONT_SIZE = 22;

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y)
    {
        $this->_mockup->getImage()->imagettftext(
            self::FONT_SIZE, 
            0, 
            FaZend_Pan_Ui_Mockup::INDENT, 
            $y + self::FONT_SIZE, 
            $this->_mockup->getImage()->getColor('mockup.content.title'), 
            $this->_mockup->getImage()->getFont('mockup.content.title'), 
            self::parse($this->label)
        );

        return self::FONT_SIZE * 2;
    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html()
    {
        return '<h1>' . nl2br(self::parse($this->label)) . '</h1>';
    }

}
