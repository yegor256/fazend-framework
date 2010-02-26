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
     * Reconfigure class according to reflection information
     *
     * @param Reflector Information about entity
     * @return void
     **/
    public function reflect(Reflector $reflector)
    {
        assert($reflector instanceof Zend_Reflection_File);
        $this->_moveTo(FaZend_Pan_Analysis_Component_System::getInstance());
    }

    /**
     * Get name of the file
     *
     * @return string
     */
    public function getName() 
    {
        return preg_replace(
            '/[^a-zA-Z0-9]+/',
            '-', 
            pathinfo($this->_name, PATHINFO_BASENAME)
        );
    }
    
    /**
     * Get tag for "see" tag
     *
     * @return string
     */
    public function getTraceTag() 
    {
        return $this->_name;
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
