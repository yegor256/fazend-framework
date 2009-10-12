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
 * Simplified interface for exception throwing
 *
 * @package Exception
 */
class FaZend_Exception extends Exception {

    /**
     * Creates exception class on fly
     *
     * <code>
     * if (.. something wrong ..) {
     *   throw Zend_Exception::create('Model_User_BadUser', 'Your user Id is incorrect');
     * }
     * </code>
     *
     * @param string Exception class name, will be created or loaded
     * @param string Message to be sent inside this class
     * @return boolean
     */
    public static function raise($class, $message = false, $parent = 'Zend_Exception') {

        // exception class should either exist or should
        // be dynamically created
        self::_declareClass($class, $parent);

        // throw this class as exception
        throw new $class($message);

    }

    /**
     * Automatically create class or load it
     *
     * @param string Exception class name, will be created or loaded
     * @param string Parent class
     * @return void
     */
    public static function _declareClass($class, $parent = 'Zend_Exception') {

        if (class_exists($class, false))
            return;
            
        // try to load it, maybe it exists
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->suppressNotFoundWarnings(true);

        // no, it doesn't exist - so we create it!
        if (@$autoloader->autoload($class))
            return;
                
        // declare the parent
        self::_declareClass($parent, 'Zend_Exception');

        // sanity check, in case they are equal
        if ($class == $parent)
            return;

        // dynamically declare this class
        eval("class {$class} extends {$parent} {};");    

    }

}
