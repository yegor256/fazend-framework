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
     * Shall we protocol the actions performed?
     *
     * @var boolean
     */
    protected $_verbose = false;
    
    /**
     * Set of injected values/objects
     *
     * @var array
     * @see inject()
     */
    protected $_injected = array();

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
     * Set verbose mode
     *
     * @param boolean Shall we protocol all calls?
     * @return $this
     */
    public function setVerbose($verbose = true) 
    {
        $this->_verbose = $verbose;
        return $this;
    }
    
    /**
     * Inject some value
     *
     * @param mixed Value to inject
     * @return $this
     */
    public function inject($value) 
    {
        $this->_injected['i' . (count($this->_injected) + 1)] = $value;
        return $this;
    }
    
    /**
     * Already has injected value?
     *
     * @param string Name of injected var, like "i1", "i2", etc
     * @return void
     */
    public function hasInjected($name) 
    {
        return array_key_exists($name, $this->_injected);
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
                "Can't instantiate the class using: '{$data}'"
            );
        }

        $class = __CLASS__ . '_' . ucfirst($class);
        return new $class($data);
    }
    
    /**
     * Execute the callback and return its result
     *
     * @param array Associated array of params
     * @return mixed
     */
    public final function callAssociated(array $args) 
    {
        $result = $this->_call($args);
        
        if ($this->_verbose) {
            $mnemos = array();

            foreach ($args as $arg) {
                // this is necessary for logging (see below)
                switch (true) {
                    case is_bool($arg):
                        $mnemo = $arg ? 'TRUE' : 'FALSE';
                        break;
                    case is_scalar($arg):
                        $mnemo = "'" . cutLongLine($arg) . "'";
                        break;
                    case is_object($arg):
                        $mnemo = get_class($arg);
                        break;
                    case is_null($arg):
                        $mnemo = 'NULL';
                        break;
                    default:
                        $mnemo = '???';
                        break;
                }
                $mnemos[] = $mnemo;
            }

            // log this operation
            logg(
                "Calling %s(%s)",
                $this->getTitle(),
                implode(', ', $mnemos)
            );
        }
        
        return $result;
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
        foreach ($args as $id=>$arg) {
            $args['a' . ($id + 1)] = $arg;
            unset($args[$id]);
        }
        return $this->callAssociated($args);
    }
    
    /**
     * Returns an array of inputs expected by this callback
     *
     * @return string[]
     */
    abstract public function getInputs();
    
    /**
     * Returns a short name of the callback
     *
     * @return string
     */
    abstract public function getTitle();
    
    /**
     * Calculate and return
     *
     * @param array Array of params
     * @return mixed
     */
    abstract protected function _call(array $args); 

}
