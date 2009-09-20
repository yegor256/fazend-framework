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
 * One simple validator
 *
 * Use it like this:
 *
 * <code>
 * function doSomething($name) {
 *     validate()
 *         ->type($name, 'string', 'Name should be in type string only')
 *         ->regexp($name, '/^[\w\s]+$/', 'Letters and spaces only')
 *         ->false(isset($this->_users[$name]), 'User with this name already exists');
 *     // do something if the validation passed
 * }
 * </code>
 *
 * @package FaZend
 */
class FaZend_Validator {

    /**
     * Simple instance, to avoid multiple instances
     *
     * @var FaZend_Validator
     */
    protected static $_instance = null;
    
    /**
     * Creates simple validator and returns it
     *  
     * @return FaZend_Validator
     **/
    public static function factory() {
        if (is_null(self::$_instance))
            self::$_instance = new FaZend_Validator();
        return self::$_instance;
    }

    /**
     * Call decorator
     *
     * @param string Name of the method
     * @param array List of arguments
     * @return value
     */
    public function __call($method, array $args) {
        // first param is subject
        $subject = array_shift($args);

        $class = 'FaZend_Validator_' . ucfirst($method);
        $validator = new $class($subject);

        // last param is message
        if (count($args))
            $message = array_pop($args);
        else
            $message = $validator->defaultMessage;

        if (!call_user_func_array(array($validator, 'validate'), $args)) {
            FaZend_Exception::raise($class . '_Failure', $message . 
                ' (' . 
                    (is_scalar($subject) ? 
                        (is_bool($subject) ? ($subject ? 'TRUE' : 'FALSE') : $subject) : 
                    get_class($subject)) . 
                ')', 'FaZend_Validator_Failure');
        }

        return $this;
    }

}
