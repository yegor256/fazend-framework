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
 * One abstract diagram
 *
 * @package AnalysisModeller
 * @subpackage Diagram
 */
abstract class FaZend_AnalysisModeller_Diagram_Abstract {

    /**
     * Name of the diagram
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Component to work with
     *
     * @var FaZend_AnalysisModeller_Component_Abstract
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
        
        $exp = explode(FaZend_AnalysisModeller_Diagram::SEPARATOR, $name);
        array_pop($exp); // remove the type of diagram
        
        $this->_component = FaZend_AnalysisModeller_Component_System::getInstance()
            ->findByFullName(implode(FaZend_AnalysisModeller_Component::SEPARATOR, $exp));
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
        return substr(strrchr($this->_name, FaZend_AnalysisModeller_Diagram::SEPARATOR), 1);
    }

    /**
     * Get name of another diagram with the same component, but different type
     *
     * @param string Type of the diagram
     * @return string Name of the diagram
     */
    public function getAnotherType($type) {
        $name = $this->getName();
        $name = substr($name, 0, strrpos($name, FaZend_AnalysisModeller_Diagram::SEPARATOR)+1) . $type;
        return $name;
    }

    /**
     * Get list of components to show on this diagram, besides the central component
     *
     * @return FaZend_AnalysisModeller_Component_Abstract[]
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
