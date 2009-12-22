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
 * Abstract class for all POS objects
 * 
 * @package Pos
 */
abstract class FaZend_Pos_Abstract implements ArrayAccess
{
    
    /**
     * Root object
     *
     * @var FaZend_Pos_Abstract
     **/
    protected static $_root;
    
    /**
     * Name of root class
     *
     * @var string
     **/
    protected static $_rootClass = 'FaZend_Pos_Root';
   
    /**
     * Contains the system properties for this object
     * 
     * @var FaZend_Pos_Properties
     */
    protected $_ps = null;

    /**
     * Set name of root class
     *
     * @param string Name of root class
     * @return void
     **/
    public static function setRootClass($rootClass) 
    {
        self::$_rootClass = $rootClass;
    }

    /**
     * Get root object
     *
     * @return FaZend_Pos_Abstract
     **/
    public static function root() 
    {
        if (!isset(self::$_root))
            self::$_root = new self::$_rootClass();
        return self::$_root;
    }

    /**
     * Clean the entire structure
     *
     * @return void
     **/
    public static function clean() 
    {
        self::$_root = null;
    }

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_init();
    }

    /**
     * User setup code.  This should be implemented by the user to initialize
     * any variables for this object.
     * 
     * @return void
     */
    protected function _init()
    {
        //...
    }

    /**
     * Accesses the system properties for this object.
     * 
     * @return FaZend_Pos_Properties
     */
    public final function ps()
    {
        if (!isset($this->_ps))
            $this->_ps = new FaZend_Pos_Properties($this);
        return $this->_ps;
    }

    /**
     * Magic method implementation for setting public properties on the object
     * 
     * @param string Name of property
     * @param mixed Value of it
     * @return void
     */
    public function __set($name, $value)
    {
        return $this->ps()->setProperty($name, $value);
    }

    /**
     * Magic getter
     * 
     * @param mixed $name 
     * @return TODO
     */
    public function __get($name)
    {
        return $this->ps()->getProperty($name);
    }

    /**
     * For ArrayAccess
     * 
     * @param string Key of the item
     * @return boolean
     */
    public function offsetExists($name)
    {
        return $this->ps()->hasItem($name);
    }

    /**
     * For ArrayAccess
     * 
     * @param string Key of the item
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->ps()->getItem($name);
    }

    /**
     * for ArrayAccess
     * 
     * @param string Key
     * @param string Value
     * @return void
     */
    public function offsetSet($name, $value)
    {
        return $this->ps()->setItem($name, $value);
    }

    /**
     * For ArrayAccess
     * 
     * @param string Key
     * @return void
     */
    public function offsetUnset($name)
    {
        return $this->ps()->unsetItem($name, $value);
    }

    /**
     * Check the existence of property
     * 
     * @param mixed Key
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->ps()->hasProperty($name) && 
            !is_null($this->ps()->getProperty($name));
    }

    /**
     * Unset the property
     * 
     * @param string Name of the property to unset
     * @return void
     */
    public function __unset($name)
    {
        return $this->ps()->unsetProperty($name);
    }
    
    /**
     * Called after unserialize()
     *
     * @return void
     **/
    public function __wakeup() 
    {
        $this->ps()->load(true);
    }

}
