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
 * System component, central
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
class FaZend_Pan_Analysis_Component_Class extends FaZend_Pan_Analysis_Component_Abstract
{

    /**
     * Reconfigure class according to reflection information
     *
     * @param Reflector Information about entity
     * @return void
     **/
    public function reflect(Reflector $reflector)
    {
        assert($reflector instanceof Zend_Reflection_Class);
        $this->_name = $reflector->getName();
        
        if ($reflector->getDocComment())
            $this->_convertTagsToTraces($reflector->getDocblock());

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
    public function svg(Zend_View $view, $type, $x, $y)
    {
        $title = $this->_cutTitle($this->getName());
        $font = FaZend_Pan_Analysis_Component::FONT_SIZE;
        $line = FaZend_Pan_Analysis_Component::STROKE_WIDTH;
        
        // width and height of the image
        list($width, ) = FaZend_Image::getTextDimensions($title, $font);
        $width = max($font * 5, $width);
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
