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
 * @package Validator
 */
final class FaZend_Validator
{

    /**
     * Simple instance, to avoid multiple instances
     *
     * @var FaZend_Validator
     */
    protected static $_instance = null;
    
    /**
     * Plugin loader
     *
     * @var Zend_Loader_PluginLoader
     */
    protected $_loader;
    
    /**
     * Creates simple validator and returns it
     *  
     * @return FaZend_Validator
     */
    public static function factory()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new FaZend_Validator();
        }
        return self::$_instance;
    }

    /**
     * Construct validator class
     *
     * @return void
     */
    public final function __construct()
    {
        $this->_loader = new Zend_Loader_PluginLoader(
            array(
                'Zend_Validate' => ZEND_PATH . '/Validate',
                'FaZend_Validate' => FAZEND_PATH . '/Validate',
                'Validator' => APPLICATION_PATH . '/validators',
            )
        );
    }

    /**
     * Call decorator
     *
     * @param string Name of the method
     * @param array List of arguments
     * @return value
     * @throws FaZend_Validator_Exception
     */
    public function __call($method, array $args)
    {
        // not enough params provided?
        if (!count($args)) {
            FaZend_Exception::raise(
                'FaZend_Validator_Exception', 
                "At least one param required for {$method} validator",
                'Zend_Validate_Exception'
            );
        }
        
        // first param is subject to validate
        $subject = array_shift($args);

        // load this validator
        $class = $this->_loader->load(ucfirst($method));
        
        // understand what is required by its constructor
        $rc = new ReflectionClass($class);
        $rm = $rc->getConstructor();
        
        // this class requires params in construct?
        if ($rm) {
            $params = $rm->getNumberOfParameters();
        } else {
            $params = 0;
        }
        
        // not enough params provided?
        if (count($args) < $params) {
            FaZend_Exception::raise(
                'FaZend_Validator_Exception', 
                "Validator {$class} requires {$params} parameters, only " . count($args) . ' provided',
                'Zend_Validate_Exception' 
            );
        }
        
        // too many? including the message (last param)
        if (count($args) > $params + 1) {
            FaZend_Exception::raise(
                'FaZend_Validator_Exception', 
                "Validator $class requires just $params parameters, while " . count($args) . ' provided, what for?',
                'Zend_Validate_Exception'
            );
        }
        
        try {
            // initialize validator with dynamic list of params
            $call = '$validator = new $class(';
            for ($i=0; $i<$params; $i++) {
                $call .= ($i > 0 ? ', ' : false) . "\$args[{$i}]";
            }
            $call .= ');';
            eval($call);

            // last param is message
            if (count($args) == $params + 1) {
                $message = array_pop($args);
            }
        } catch (Zend_Validate_Exception $e) {
            FaZend_Exception::raise(
                'FaZend_Validator_Exception', 
                $e->getMessage(), 
                'Zend_Validate_Exception'
            );
        }

        try {
            // try to validate it through validator
            if (!$validator->isValid($subject)) {
                FaZend_Exception::raise(
                    'FaZend_Validator_Exception',
                    "Validator " . get_class($validator) . "->isValid() returned FALSE",
                    'Zend_Validate_Exception'
                );
            }
            // return for fluent interface
            return $this;
        } catch (Zend_Validate_Exception $e) {
            if (!isset($message)) {
                $message = implode(
                    '; ', 
                    array_merge(
                        $e->getMessage() ? array($e->getMessage()) : array(),
                        $validator->getMessages()
                    )
                );
            }
            FaZend_Exception::raise(
                'FaZend_Validator_Exception', 
                $message, 
                'Zend_Validate_Exception'
            );
        }
    }

}
