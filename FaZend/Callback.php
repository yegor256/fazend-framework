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
 * @version $Id: Date.php 1601 2010-02-08 12:10:51Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Universal callback holder and manipulator
 *
 * @package Callback
 */
abstract class FaZend_Callback
{

    /**
     * Callback data, in different way
     *
     * @var string|function|array
     */
    protected $_data;

    /**
     * Construct the class
     *
     * @param string|function|array
     * @return void
     */
    private function __construct($data)
    {
        $this->_data = $data;
    }
    
    /**
     * Create a new object, a callback
     *
     * @param string|function|array
     * @return FaZend_Callback
     * @see __construct()
     * @throws FaZend_Callback_InvalidData
     */
    public static function factory($data) 
    {
        if ($data instanceof FaZend_Callback) {
            return $data;
        }
        
        switch (true) {
            // simple type-caster
            case $data === 'string':
            case $data === 'float':
            case $data === 'integer':
            case $data === 'boolean':
                $class = $data;
                break;
                
            // caller to some method in some class    
            case is_array($data) && (count($data) == 2):
                $class = 'method';
                break;
                
            // maybe it's a method
            case is_callable($data):
                $class = 'callable';
                break;
                
            // PHP string?
            case is_string($data):
                $class = 'eval';
                break;
                
            // maybe it's a boolean constant already?
            case is_bool($data):
                return new FaZend_Callback_Constant($data);
        }

        if (!isset($class)) {
            FaZend_Exception::raise(
                'FaZend_Callback_InvalidData', 
                "Can't instantiate the class using the data provided"
            );
        }

        $class = __CLASS__ . '_' . ucfirst($class);
        return new $class($data);
    }
    
    /**
     * Execute the callback and return its result
     *
     * @param mixed Some params, which are required by the callback
     * @return mixed
     */
    public final function call(/* param, param, ... */) 
    {
        $args = func_get_args();
        return $this->_call($args);
    }
    
    /**
     * Calculate and return
     *
     * @param array Array of params
     * @return mixed
     */
    abstract protected function _call(array $args); 

}
