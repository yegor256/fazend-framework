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
 * @package AnalysisModeller
 * @subpackage Diagram
 */
class FaZend_Pan_Analysis_Diagram_Partof extends FaZend_Pan_Analysis_Diagram_Abstract {

    /**
     * Get list of components to show on this diagram, besides the central component
     *
     * @return FaZend_Pan_Analysis_Component_Abstract[]
     */
    public function getComponentsToShow() {
        $list = array();
        foreach ($this->_component as $component) {
            if (!($component instanceof FaZend_Pan_Analysis_Component_Class))
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
        
        $components = $this->getComponentsToShow();
        
        if (count($components)) {
            $delta = 180/min(count($components), $maxToShow);
            $angle = 270 + 90/min(count($components), $maxToShow);
            $radius = 60;
            $total = 0;
        
            while (count($components) && ($total < $maxToShow)) {
                $component = array_shift($components);
                $x = $centerX + $radius * sin(deg2rad($angle));
                $y = $centerY + $radius * cos(deg2rad($angle));
                
                $svg .= FaZend_Pan_Analysis_Component_Abstract::makeSvg('line', array(
                    'x1' => $centerX,
                    'y1' => $centerY,
                    'x2' => $x,
                    'y2' => $y,
                    'stroke' => '#' . FaZend_Image::UML_BORDER,
                    'stroke-width' => FaZend_Pan_Analysis_Component::STROKE_WIDTH,
                    ));
                
                $svg .= $component->svg($view, 'partof', $x, $y);
                $angle += $delta;
                $total++;
            }
        }

        // put central element to the center
        $svg .= $this->_component->svg($view, 'partof', $centerX, $centerY);
        
        return $svg;
    }

}
