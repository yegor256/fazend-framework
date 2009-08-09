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
     * @return value|false
     */
    public function __get($property) {
        if (!isset($this->$property))
            return false;
        return $this->$property;    
    }

    /**
     * Get the property which is protected
     *
     * @return value|false
     */
    public function __call($method, $args) {

        $matches = array();
        if (!preg_match('/^(get|set)(.+)$/', $method, $matches))
            FaZend_Exception::raise('FaZend_StdObject_MissedMethod', "Method '{$method}' is not defined in " . get_class($this));

        $property = $matches[2];
        $property[0] = strtolower($property[0]);
        $property = '_' . $property;

        if (($matches[1] == 'get') && (property_exists($this, $property)))
            return $this->$property;

        if (($matches[1] == 'set') && (property_exists($this, $property))) {
            $this->$property = $args[0];
            return;
        }

        FaZend_Exception::raise('FaZend_StdObject_MissedProperty', "Property '{$property}' is not defined in " . get_class($this));

    }

    /**
     * Serialize all local data into array
     *
     * @return string
     */
    protected function _serialize() {

        $properties = array();

        $reflector = new ReflectionClass(get_class($this));
        foreach ($reflector->getProperties() as $prop) {
            $name = $prop->getName();
            $properties[$name] = $this->$name;
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
