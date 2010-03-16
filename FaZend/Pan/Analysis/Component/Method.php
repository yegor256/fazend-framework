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
 * Method in class
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
class FaZend_Pan_Analysis_Component_Method extends FaZend_Pan_Analysis_Component_Abstract
{

    /**
     * Reconfigure class according to reflection information
     *
     * @param Reflector Information about entity
     * @return void
     **/
    public function reflect(Reflector $reflector)
    {
        assert($reflector instanceof Zend_Reflection_Method);
        $this->_name = $reflector->getName();

        if ($reflector->getDocComment()) {
            $this->_convertTagsToTraces($reflector->getDocblock());
        }

        // find all todo tags and add them into $this->_todoTags
        $this->_findTodoTags($reflector->getDocblock());
    }
    
    /**
     * Get tag for "see" tag
     *
     * @return string
     */
    public function getTraceTag() 
    {
        return $this->_parent->getTraceTag() . '::' . $this->_name . '()';
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
    public function svg(Zend_View $view, $type, $x, $y)
    {
    }

}
