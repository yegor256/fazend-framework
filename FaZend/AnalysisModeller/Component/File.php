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
 * Just one file
 *
 * @category FaZend
 * @package AnalysisModeller
 */
abstract class FaZend_AnalysisModeller_Component_File extends FaZend_AnalysisModeller_Component_Abstract {

    /**
     * Build SVG of the component and returns it
     *
     * @param Zend_View View to render
     * @param string Type of diagram to draw
     * @param integer X-coordinate
     * @param integer Y-coordinate
     * @return string
     */
    public function svg(Zend_View $view, $type, $x, $y) {
    }
    
}
