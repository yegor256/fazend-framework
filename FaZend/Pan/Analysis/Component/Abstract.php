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
 * One component (class, package, category, subcategory, method, etc.)
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
abstract class FaZend_Pan_Analysis_Component_Abstract extends ArrayIterator
{

    const XLINK_NS = 'http://www.w3.org/1999/xlink';

    /**
     * Name of the component, like 'User' or 'AccessController'
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Parent component
     *
     * @var FaZend_Pan_Analysis_Component
     */
    protected $_parent;
    
    /**
     * Constructor
     *
     * @param FaZend_Pan_Analysis_Component_Abstract Parent
     * @param string Name of the component
     * @return void
     */
    public function __construct(FaZend_Pan_Analysis_Component_Abstract $parent = null, $name)
    {
        $this->_parent = $parent;
        $this->_name = $name;
    }

    /**
     * Simplified factory method
     *
     * @param string Type of the component
     * @param string Name of the component
     * @param Reflector Information about this component
     * @return FaZend_Pan_Analysis_Component_Abstract
     */
    public function factory($type, $name, Reflector $reflector = null)
    {
        $className = 'FaZend_Pan_Analysis_Component_' . ucfirst($type);
        $component = new $className($this, $name);
        $this[] = $component;
        
        if (!is_null($reflector))
            $component->reflect($reflector);
            
        return $component;
    }

    /**
     * Reconfigure class according to reflection information
     *
     * @param Reflector Information about entity
     * @return void
     **/
    abstract public function reflect(Reflector $reflector);

    /**
     * Returns name of the component
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns type of the component
     *
     * @return string
     */
    public function getType()
    {
        return lcfirst(substr(get_class($this), strlen('FaZend_Pan_Analysis_Component_')));
    }

    /**
     * Returns full name of the component
     *
     * @return string
     */
    public function getFullName()
    {
        if (!$this->_parent)
            return $this->getName();
        return $this->_parent->getFullName() . FaZend_Pan_Analysis_Component::SEPARATOR . $this->getName();
    }
    
    /**
     * Find child component by name
     *
     * @param string Name of the componen to find
     * @return FaZend_Pan_Analysis_Component_Abstract
     */
    public function find($name)
    {
        foreach ($this as $component)
            if ($component->getName() == $name)
                return $component;
        FaZend_Exception::raise('FaZend_Pan_Analysis_Component_NotFound', 
            "Component not found: '{$this->getName()}'::find('{$name}')");
    }

    /**
     * Find child component by name, or make it
     *
     * @param string Type of the component to make if not found
     * @param string Name of the component to find
     * @return FaZend_Pan_Analysis_Component_Abstract
     */
    public function make($type, $name)
    {
        try {
            $component = $this->find($name);
        } catch (FaZend_Pan_Analysis_Component_NotFound $e) { 
            $component = $this->factory($type, $name);
        }
        return $component;
    }

    /**
     * Convert component name to diagram, that explains him
     *
     * @param string Type of diagram to tell about this component
     * @return string
     **/
    public function getDiagramName($type)
    {
        return str_replace(
            FaZend_Pan_Analysis_Component::SEPARATOR,
            FaZend_Pan_Analysis_Diagram::SEPARATOR,
            $this->getFullName()) . FaZend_Pan_Analysis_Diagram::SEPARATOR . $type;
    }

    /**
     * Create svg text
     *
     * @param string Name of the node in SVG
     * @param array List of attributes, associative array
     * @param string Content of the node
     * @return string SVG node
     **/
    public static function makeSvg($name, array $options = array(), $content = null)
    {
        $svg = "\n<" . $name . ' ';
        foreach ($options as $key=>$value)
            $svg .= $key . '="' . $value . '" ';
        return $svg . '>' . $content . '</' . $name . ">\n";
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
    abstract public function svg(Zend_View $view, $type, $x, $y);

    /**
     * Move component to a new destination
     *
     * @param FaZend_Pan_Analysis_Component_Abstract Destination to move to
     * @return $this
     **/
    protected function _moveTo(FaZend_Pan_Analysis_Component_Abstract $destination)
    {
        foreach ($this->_parent as $key=>$component)
            if ($component == $this)
                unset($this->_parent[$key]);
        $destination[] = $this;
        $this->_parent = $destination;
        return $this;
    }
    
    /**
     * Cut title to the normal form
     *
     * @return string
     **/
    protected function _cutTitle($text)
    {
        if (strlen($text) < 10)
            return $text;
        if (strpos($text, '_') === false)
            return $text;
        return '...' . strrchr($text, '_');
    }
    
    /**
     * Relocate me in the tree
     *
     * @return void
     **/
    protected function _relocate(Reflector $reflector)
    {
        $doc = $reflector->getDocblock();
        if (false !== $doc->getTag('category'))
            $this->_moveTo($this->_parent->make('category', trim($doc->getTag('category')->getDescription(), "\r\t\n ")));
            
        if (false !== $doc->getTag('package'))
            $this->_moveTo($this->_parent->make('package', trim($doc->getTag('package')->getDescription(), "\r\t\n ")));

        if (false !== $doc->getTag('subpackage'))
            $this->_moveTo($this->_parent->make('package', trim($doc->getTag('subpackage')->getDescription(), "\r\t\n ")));
    }
    
}
