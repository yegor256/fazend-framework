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
 * One diagram, style 'part-of'
 *
 * @category FaZend
 * @package FaZend_AnalysisModeller
 * @subpackage FaZend_AnalysisModeller_Diagram
 */
class FaZend_AnalysisModeller_Diagram_Partof extends FaZend_AnalysisModeller_Diagram_Abstract {

    /**
     * Get list of components to show on this diagram, besides the central component
     *
     * @return FaZend_AnalysisModeller_Component_Abstract[]
     */
    public function getComponentsToShow() {
        $list = array();
        foreach ($this->_component as $component) {
            if (!($component instanceof FaZend_AnalysisModeller_Component_Class))
                continue;
            $list[] = $component;
        }
        return $list;
    }
    
    /**
     * Add specific SVG content to the diagram
     *
     * @param Zend_View Instance of view to use for rendering
     * @return string
     */
    public function svg(Zend_View $view) {
        $svg = '';
        
        $centerX = 40;
        $centerY = 25;
        $maxToShow = 8;
        
        // put central element to the center
        $svg .= $this->_component->svg($view, 'partof', $centerX, $centerY - 10);
        
        $components = $this->getComponentsToShow();
        
        if (!count($components))
            return $svg;
        
        $delta = 180/min(count($components), $maxToShow);
        $angle = 270 + 90/min(count($components), $maxToShow);
        $radius = 60;
        $total = 0;
        while (count($components) && ($total < $maxToShow)) {
            $component = array_shift($components);
            $x = $centerX + $radius * sin(deg2rad($angle));
            $y = $centerY + $radius * cos(deg2rad($angle));
            $svg .= $component->svg($view, 'partof', $x, $y);
            $angle += $delta;
            $total++;
        }
        return $svg;
    }

}
