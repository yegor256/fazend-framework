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
 * Just one file
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
abstract class FaZend_Pan_Analysis_Component_File extends FaZend_Pan_Analysis_Component_Abstract
{

    /**
     * Build SVG of the component and returns it
     *
     * @param Zend_View View to render
     * @param string Type of diagram to draw
     * @param integer X-coordinate
     * @param integer Y-coordinate
     * @return string
     */
    public function svg(Zend_View $view, $type, $x, $y)
    {
    }
    
}
