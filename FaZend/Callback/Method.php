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
 * Callback, which uses methods in classes
 *
 * @package Callback
 */
class FaZend_Callback_Method extends FaZend_Callback
{
    
    /**
     * Class or object
     *
     * @var string|object
     */
    protected $_class;
    
    /**
     * Method
     *
     * @var string
     */
    protected $_method;

    /**
     * Construct the class
     *
     * @param array Callback
     * @return void
     */
    public function __construct($data)
    {
        list($this->_class, $this->_method) = $data;
    }

    /**
     * Returns a short name of the callback
     *
     * @return string
     */
    public function getTitle()
    {
        return (is_string($this->_class) ? $this->_class : get_class($this->_class)) . 
        '::' . $this->_method;
    }
    
    /**
     * Returns an array of inputs expected by this callback
     *
     * @return string[]
     */
    public function getInputs()
    {
        // prepare method calling params for this button/callback
        $rMethod = new ReflectionMethod($this->_class, $this->_method);

        $inputs = array();
        // run through all required paramters. required by method
        foreach ($rMethod->getParameters() as $param) {
            $inputs[] = $param->name;
        }
        return $inputs;
    }
    
    /**
     * Calculate and return
     *
     * @param array Array of params
     * @return mixed
     * @throws FaZend_Callback_Method_InvalidMethodException
     */
    protected function _call(array $args)
    {
        if (!method_exists($this->_class, $this->_method)) {
            FaZend_Exception::raise(
                'FaZend_Callback_Method_InvalidMethodException',
                "Method '{$this->_method}' is not found"
            );
        }
        return call_user_func_array(array($this->_class, $this->_method), $args);
    }

}
