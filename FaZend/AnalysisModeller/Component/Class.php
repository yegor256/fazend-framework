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
 * System component, central
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
class FaZend_AnalysisModeller_Component_Class extends FaZend_AnalysisModeller_Component_Abstract {

    /**
     * Reconfigure class according to reflection information
     *
     * @param Reflector Information about entity
     * @return void
     **/
    public function reflect(Reflector $reflector) {
        assert($reflector instanceof Zend_Reflection_Class);
        $this->_name = $reflector->getName();

        // change my location
        $this->_relocate($reflector);
                    
        foreach ($reflector->getMethods() as $method)
            $this->factory('method', null, $method);
    }
    
    /**
     * Build SVG of the component and returns it
     *
     * @param Zend_View View to render
     * @param string Type of diagram to draw
     * @param integer X-coordinate of the center
     * @param integer Y-coordinate of the center
     * @return string
     */
    public function svg(Zend_View $view, $type, $x, $y) {
        $title = $this->_cutTitle($this->getName());
        $font = FaZend_AnalysisModeller_Component::FONT_SIZE;
        $line = FaZend_AnalysisModeller_Component::STROKE_WIDTH;
        
        // width and height of the image
        $width = $this->_textWidth($title) * $font;
        $height = $font * 4;
        
        // left top corner coordinate
        $cornerX = $x - $width/2;
        $cornerY = $y - $height/2;
        
        return 
        
        self::makeSvg('rect', array(
            'x' => $cornerX,
            'y' => $cornerY,
            'width' => $width,
            'height' => $height,
            'fill' => '#' . FaZend_Image::UML_FILL,
            'stroke' => '#' . FaZend_Image::UML_BORDER,
            'stroke-width' => $line)) .
            
        self::makeSvg('line', array(
            'x1' => $cornerX,
            'y1' => $cornerY + $font * 2.5,
            'x2' => $cornerX + $width,
            'y2' => $cornerY + $font * 2.5,
            'stroke' => '#' . FaZend_Image::UML_BORDER,
            'stroke-width' => $line)) .

        self::makeSvg('line', array(
            'x1' => $cornerX,
            'y1' => $cornerY + $font * 3,
            'x2' => $cornerX + $width,
            'y2' => $cornerY + $font * 3,
            'stroke' => '#' . FaZend_Image::UML_BORDER,
            'stroke-width' => $line)) .

        self::makeSvg('a', array(
            'target' => '_parent',
            'xlink:href' => $view->url(array(
                'action' => 'index', 
                'diagram' => $this->getDiagramName($type)), 'analysis'),
            'xlink:title' => $this->getFullName()),
            self::makeSvg('text', array(
                'x' => $cornerX + $font,
                'y' => $cornerY + $font * 2,
                'class' => 'text',
                'font-family' => 'Verdana',
                'font-size' => $font), $title));
                 
    }

}
