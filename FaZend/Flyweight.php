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
 * Flyweight Factory
 *
 * You can use it for storage of simple objects, that don't need to be 
 * created every time from scratch. Example:
 *
 * <code>
 * $object = FaZend_Flyweight::factory('Model_User', $this, 'me@example.com');
 * </code>
 *
 * The instance of class Model_User won't be created again if it
 * already exists in the factory. The instance will be returned.
 *
 * @package Model
 */
class FaZend_Flyweight {

    /**
     * Storage of objects
     *
     * @var array
     */
    static protected $_storage = array();

    /**
     * Instantiate and return an object
     *
     * @param string Name of the class to create
     * @param mixed Any amount of params to be passed to the constructor
     * @return mixed
     **/
    public static function factory($class /*, many params... */) {
        $args = func_get_args();
        array_shift($args); // pop out the first argument
        
        // unique object ID in the storage
        $id = self::_makeId(array_merge(array($class), $args));
        
        // if it's already here - return it
        if (isset(self::$_storage[$id]))
            return self::$_storage[$id];
        
        // initialize validator with dynamic list of params
        $call = '$object = new $class(';
        for ($i=0; $i<count($args); $i++)
            $call .= ($i > 0 ? ', ' : false) . "\$args[{$i}]";
        $call .= ');';
        eval($call);
        
        return self::$_storage[$id] = $object;
    }

    /**
     * Generate ID out of a list of params
     *
     * @param array List of args
     * @return string
     */
    public static function _makeId(array $args) {
        $id = '';
        foreach ($args as $arg) {
            if (is_scalar($arg)) {
                // kill this SPECIAL symbol from scalar arguments
                $arg = str_replace('.', '\.', $arg);
            } elseif (is_array($arg)) { 
                $arg = self::_makeId($arg);
            } else {
                $arg = '.' . spl_object_hash($arg);
            }
            $id .= '.' . $arg;
        }
        return $id;
    }
    
}
