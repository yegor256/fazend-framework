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
 * Mockup meta element, list of links
 *
 * @package FaZend 
 */
class FaZend_UiModeller_Mockup_Meta_Bar extends FaZend_UiModeller_Mockup_Meta_Abstract {

    /**
     * Draw 
     *
     * @return int Height
     */
    public function draw($y) {


    }

    /**
     * Convert to HTML
     *
     * @return string HTML image of the element
     */
    public function html() {

        $links = $this->_getOptions('/^link.*/');

        $hrefs = array();
        foreach ($links as $link)
            $hrefs[] = $this->_htmlLink($link['link'], $this->_parse($link['header']));

        return '<p>' . implode(' | ', $hrefs) . '</p>';

    }

    /**
     * Add link
     *
     * @return this
     */
    public function addLink($name, $header, $link = false) {
        $this->__set('link' . $name, array(
            'header'=>$header,
            'link'=>$link));
        return $this;
    }

}
