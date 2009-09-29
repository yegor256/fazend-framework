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
 * Simple class with nice methods
 *
 * @package FaZend
 */
class FaZend_StdObject {

    /**
     * Simple static creator
     *
     * @return FaZend_StdObject
     */
    public static function create() {
        return new FaZend_StdObject();
    }

    /**
     * Set the value of some property
     *
     * @return FaZend_StdObject
     */
    public function set($property, $value) {
        $this->$property = $value;
        return $this;    
    }

    /**
     * Get the property which is not set yet
     *
     * If you want to transfer calls like $this->name to functions like
     * $this->_getName() you should just declare such method and that's it.
     *
     * @return value|false
     */
    public function __get($property) {

        // try this method, if it exists
        if (!property_exists($this, $property) && method_exists($this, $methodName = '_get' . ucfirst($property)))
            return call_user_func(array($this, $methodName));

        // if the property is not set - return FALSE
        if (!isset($this->$property))
            return false;

        // otherwise - return property
        return $this->$property;
        
    }

    /**
     * Get the property which is protected
     *
     * @return value|false
     */
    public function __call($method, $args) {

        $matches = array();
        if (!preg_match('/^(get|set)(.+)$/', $method, $matches)) {
            FaZend_Exception::raise('FaZend_StdObject_MissedMethod', 
                "Method '{$method}' is not defined in " . get_class($this));
        }

        $property = $matches[2];
        $property[0] = strtolower($property[0]);
        $property = '_' . $property;

        if (($matches[1] == 'get') && (property_exists($this, $property)))
            return $this->$property;

        if (($matches[1] == 'set') && (property_exists($this, $property))) {
            $this->$property = $args[0];
            return $this;
        }

        FaZend_Exception::raise('FaZend_StdObject_MissedProperty', 
            "Property '{$property}' is not defined in " . get_class($this));

    }

    /**
     * Serialize all local data into array
     *
     * @return string
     */
    protected function _serialize() {

        $properties = array();

        foreach ($this as $name=>$value) {
            $properties[$name] = $value;
        }

        return serialize($properties);

    }

    /**
     * UnSerialize all local data from string array
     *
     * @param string Serialized array
     * @return void
     */
    protected function _unserialize($str) {

        $properties = unserialize($str);
        foreach ($properties as $name=>$value) {
            $this->$name = $value;
        }

    }

}
