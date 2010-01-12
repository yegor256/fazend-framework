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
 * One abstract diagram
 *
 * @package AnalysisModeller
 * @subpackage Diagram
 */
abstract class FaZend_Pan_Analysis_Diagram_Abstract {

    /**
     * Name of the diagram
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Component to work with
     *
     * @var FaZend_Pan_Analysis_Component_Abstract
     */
    protected $_component;
    
    /**
     * Constructor
     *
     * @param string Name of the diagram, like 'System.Model.User.part-of'
     * @return void
     */
    public function __construct($name) {
        $this->_name = $name;
        
        $exp = explode(FaZend_Pan_Analysis_Diagram::SEPARATOR, $name);
        array_pop($exp); // remove the type of diagram
        
        $this->_component = FaZend_Pan_Analysis_Component_System::getInstance()
            ->findByFullName(implode(FaZend_Pan_Analysis_Component::SEPARATOR, $exp));
    }

    /**
     * Returns name of the diagram
     *
     * @return string Name of the diagram
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Returns type of the diagram
     *
     * @return string Type of the diagram
     */
    public function getType() {
        return substr(strrchr($this->_name, FaZend_Pan_Analysis_Diagram::SEPARATOR), 1);
    }

    /**
     * Get name of another diagram with the same component, but different type
     *
     * @param string Type of the diagram
     * @return string Name of the diagram
     */
    public function getAnotherType($type) {
        $name = $this->getName();
        $name = substr($name, 0, strrpos($name, FaZend_Pan_Analysis_Diagram::SEPARATOR)+1) . $type;
        return $name;
    }

    /**
     * Get list of components to show on this diagram, besides the central component
     *
     * @return FaZend_Pan_Analysis_Component_Abstract[]
     */
    public function getComponentsToShow() {
        return $this->_component;
    }
    
    /**
     * Add specific SVG content to the diagram
     *
     * @param Zend_View Instance of view to use for rendering
     * @return string
     */
    abstract public function svg(Zend_View $view);
    
}
