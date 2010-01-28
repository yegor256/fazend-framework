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
 * Mockup meta element, list of links
 *
 * @package UiModeller
 * @subpackage Mockup
 */
class FaZend_Pan_Ui_Meta_Bar extends FaZend_Pan_Ui_Meta_Abstract
{

    /**
     * Draw in PNG image
     *
     * @return int Height
     */
    public function draw($y)
    {
        $links = $this->_getOptions('/^link.*/');

        $x = FaZend_Pan_Ui_Mockup::INDENT;

        foreach ($links as $link) {
            $txt = $this->_parse($link['header']);

            // @todo replace it 
            $bbox = imagettfbbox(
                FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 0, 
                $this->_mockup->getImage()->getFont('mockup.content'), 
                $txt
            );

            $this->_mockup->getImage()->imagettftext(
                FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 0, 
                $x, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE, 
                $this->_mockup->getImage()->getColor('mockup.link'), 
                $this->_mockup->getImage()->getFont('mockup.content'), 
                $txt
            );

            $this->_mockup->getImage()->imageline(
                $x, $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE + 1, 
                $x + $bbox[4], $y + FaZend_Pan_Ui_Meta_Text::FONT_SIZE + 1, 
                $this->_mockup->getImage()->getColor('mockup.link')
            );

            $x += $bbox[4] + FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 2;
        }

        return FaZend_Pan_Ui_Meta_Text::FONT_SIZE * 2;
    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html()
    {
        $links = $this->_getOptions('/^link.*/');

        $hrefs = array();
        foreach ($links as $link)
            $hrefs[] = $this->_htmlLink($link['link'], $this->_parse($link['header']));

        return '<p>' . implode(' | ', $hrefs) . '</p>';
    }

    /**
     * Add new link to the BAR
     *
     * @param string Name of the link (UNIQUE in this bar)
     * @param string Header visible to end-user
     * @param string Name of VIEW script to link here
     * @return this
     */
    public function addLink($name, $header, $link = false)
    {
        $this->__set(
            'link' . $name, 
            array(
                'header' => $header,
                'link' => $link,
            )
        );
        return $this;
    }

}
