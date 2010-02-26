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
 * Simplified interface for exception throwing
 *
 * @package Exception
 */
class FaZend_Exception
{

    /**
     * Creates exception class on fly
     *
     * <code>
     * if (.. something wrong ..) {
     *   Zend_Exception::raise(
     *     'Model_User_BadUser',
     *     'Your user Id is incorrect'
     *   );
     * }
     * </code>
     *
     * @param string Exception class name, will be created or loaded
     * @param string Message to be sent inside this class
     * @return boolean
     * @throws Zend_Exception
     */
    public static function raise($class, $message = false, $parent = 'Zend_Exception')
    {
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
    public static function _declareClass($class, $parent = 'Zend_Exception')
    {
        if (class_exists($class, false)) {
            return;
        }
            
        // try to load it, maybe it exists
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->suppressNotFoundWarnings(true);

        // no, it doesn't exist - so we create it!
        if (@$autoloader->autoload($class)) {
            return;
        }
                
        // declare the parent
        self::_declareClass($parent, 'Zend_Exception');

        // sanity check, in case they are equal
        if ($class == $parent) {
            return;
        }

        // dynamically declare this class
        eval("class {$class} extends {$parent} {};");    
    }

}
