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
 * @category FaZend
 * @package AnalysisModeller
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

        $doc = $reflector->getDocblock();
        if (false !== $doc->getTag('package'))
            $this->_moveTo($this->_parent->make('package', trim($doc->getTag('package')->getDescription(), "\r\t\n ")));

        if (false !== $doc->getTag('subpackage'))
            $this->_moveTo($this->_parent->make('package', trim($doc->getTag('subpackage')->getDescription(), "\r\t\n ")));
            
        foreach ($reflector->getMethods() as $method)
            $this->factory('method', null, $method);
    }
    
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
        $title = $this->_cutTitle($this->getName());
        $font = FaZend_AnalysisModeller_Component::FONT_SIZE;
        $line = FaZend_AnalysisModeller_Component::STROKE_WIDTH;
        $width = $this->_textWidth($title) * $font;
        
        return 
        
        $this->_makeSvg('rect', array(
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $font * 4,
            'fill' => '#' . FaZend_Image::UML_FILL,
            'stroke' => '#' . FaZend_Image::UML_BORDER,
            'stroke-width' => $line)) .
            
        $this->_makeSvg('line', array(
            'x1' => $x,
            'y1' => $y + $font * 2.5,
            'x2' => $x + $width,
            'y2' => $y + $font * 2.5,
            'stroke' => '#' . FaZend_Image::UML_BORDER,
            'stroke-width' => $line)) .

        $this->_makeSvg('line', array(
            'x1' => $x,
            'y1' => $y + $font * 3,
            'x2' => $x + $width,
            'y2' => $y + $font * 3,
            'stroke' => '#' . FaZend_Image::UML_BORDER,
            'stroke-width' => $line)) .

        $this->_makeSvg('a', array(
            'target' => '_parent',
            'xlink:href' => $view->url(array(
                'action' => 'index', 
                'diagram' => $this->getDiagramName($type)), 'analysis'),
            'xlink:title' => $this->getFullName()),
            $this->_makeSvg('text', array(
                'x' => $x + $font,
                'y' => $y + $font * 2,
                'class' => 'text',
                'font-family' => 'Verdana',
                'font-size' => $font), $title));
                 
    }

}
