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
 * In order to use FaZend_Pos you should inherit your own classes from
 * this class and use them as you wish. The only important requirement is that
 * before you do anything with your POS objects, you should attach them to
 * POS root or to some other POS object, e.g.:
 *
 * <code>
 * FaZend_Pos_Abstract::root()->obj = $obj = new Model_My_Pos_Object();
 * $obj->test = 'works good!';
 * </code>
 *
 * @package Pos
 */
abstract class FaZend_Pos_Abstract implements ArrayAccess, Countable, Iterator
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
    protected $__ps = null;
    
    /**
     * Pos ID used for serialization only
     *
     * @var integer
     * @see __sleep()
     * @see __wakeup()
     **/
    protected $__posId = null;

    /**
     * Set name of root class
     *
     * You can give your own name of the class, which will be used for
     * ROOT object. Overriding the _init() method in that class you will
     * be able to initialize your root tree before usage.
     *
     * Your ROOT class should be a child from FaZend_Pos_Root.
     *
     * @param string Name of root class
     * @return void
     **/
    public static function setRootClass($rootClass) 
    {
        self::$_rootClass = $rootClass;
        self::cleanPosMemory();
    }

    /**
     * Get root object, the main object of the entire POS tree
     *
     * @return FaZend_Pos_Abstract
     **/
    public static function root() 
    {
        if (!isset(self::$_root)) {
            self::$_root = new self::$_rootClass();
            self::$_root->init();
        }
        return self::$_root;
    }

    /**
     * Clean the entire POS structure from memory
     *
     * Be careful with this method, it is mostly used for unit testing. When
     * you're clearing the memory, you DON'T save changes to the database. All
     * your changes will be saved only during destruction of objects. Consider
     * the example:
     *
     * <code>
     * FaZend_Pos_Abstract::root()->obj = $obj = new Model_My_Pos_Object();
     * $obj->test = 'works good!';
     * FaZend_Pos_Abstract::cleanPosMemory();
     * isset(FaZend_Pos_Abstract::root()->obj->test); // return FALSE
     * </code>
     *
     * Another example, which explains how it should be done:
     *
     * <code>
     * FaZend_Pos_Abstract::root()->obj = $obj = new Model_My_Pos_Object();
     * $obj->test = 'works good!';
     * $obj->ps()->save(); // forces the object to be saved to DB
     * FaZend_Pos_Abstract::cleanPosMemory();
     * isset(FaZend_Pos_Abstract::root()->obj->test); // return TRUE
     * </code>
     *
     * @return void
     **/
    public static function cleanPosMemory() 
    {
        self::$_root = null;
        FaZend_Pos_Properties::cleanPosMemory();
    }
    
    /**
     * Constructor, you CAN'T override it!
     *
     * If you need to setup some initial (!) behavior, you should use init()
     *
     * @return void
     * @see init()
     */
    public final function __construct()
    {
        // nothing for now
    }

    /**
     * Save all changes to DB
     *
     * @return void
     **/
    public final function __destruct() 
    {
        // We don't want any exceptions to be thrown in constructor, 
        // since they will destroy the entire application framework. That's
        // why we catch them here and log them.
        try {
            $this->ps()->save(false);
        } catch (FaZend_Pos_Exception $e) {
            $msg = get_class($e) . ' in ' . get_class($this) . "::__destruct: {$e->getMessage()}";
            if (defined('TESTING_RUNNING'))
                echo $msg . "\n";
            else
                FaZend_Log::info($msg);
        }
    }

    /**
     * User setup code.  This should be implemented by the user to initialize
     * any variables for this object.
     * 
     * @return void
     */
    public function init()
    {
        // to be overriden in child classes
    }

    /**
     * Accesses the system properties for this object.
     * 
     * Anything that you might want to do with the POS object is done by means
     * of this specific class, attached to your POS object automatically. For
     * example:
     *
     * <code>
     * FaZend_Pos_Abstract::root()->obj = $obj = new Model_My_Pos_Object();
     * $obj->test = 'works good!';
     * $obj->ps()->save(); // you're saving changes to the DB
     * $ver = $obj->ps()->version; // you get current version of the object
     * FaZend_Pos_Abstract::cleanPosMemory();
     * isset(FaZend_Pos_Abstract::root()->obj->test); // return TRUE
     * </code>
     *
     * Object in the DB are static, while in PHP they are dymanic. In other
     * words, you can have many PHP objects, linked to the same DB object. 
     * For example:
     *
     * <code>
     * $car = FaZend_Pos_Abstract::root()->car;
     * $car2 = FaZend_Pos_Abstract::root()->car;
     * </code>
     *
     * As you see from the example, two PHP variables will be linked to the
     * same object in the DB. When you're making changes to one object, the
     * other will stay unchanged. To avoid such a situation we have a list
     * of already instantiated objects in FaZend_Pos_Properties::$_instances. 
     * When you are trying to create a new PHP object, but we
     * already have one in memory - we find this object and link to it, by means
     * of calling ps() with the PS object, related to the existing object.
     *
     * @param FaZend_Pos_Properties Properties to be explicitly set, if we need
     *     to link this object to already existing clone. The method will be called
     *     with this parameter specified only from FaZend_Pos_Properties::_attachTo()
     * @return FaZend_Pos_Properties
     * @see FaZend_Pos_Properties::_attachTo()
     */
    public final function ps(FaZend_Pos_Properties $ps = null)
    {
        if (!is_null($ps))
            $this->__ps = $ps;
        if (!isset($this->__ps))
            $this->__ps = new FaZend_Pos_Properties($this);
        return $this->__ps;
    }

    /**
     * Magic method implementation for setting public properties on the object
     * 
     * @param string Name of property
     * @param mixed Value of the property
     * @return void
     * @see http://php.net/manual/en/language.oop5.magic.php
     * @throws FaZend_Pos_Exception If something goes wrong with the object
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
     * @see http://php.net/manual/en/language.oop5.magic.php
     * @throws FaZend_Pos_Exception If something goes wrong with the object
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
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function offsetExists($name)
    {
        return $this->ps()->hasItem($name) &&
            !is_null($this->ps()->getItem($name));
    }

    /**
     * For ArrayAccess
     * 
     * @param string Key of the item
     * @return mixed
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function offsetGet($name)
    {
        return $this->ps()->getItem($name);
    }

    /**
     * for ArrayAccess
     * 
     * @param string|false Key or false, if the element should be added as new
     * @param string Value
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong with the object
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
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function offsetUnset($name)
    {
        return $this->ps()->unsetItem($name);
    }

    /**
     * Countable Interface method
     *
     * @return integer Total amount of ITEMS in the array
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function count() 
    {
        return iterator_count($this->ps()->itemsIterator);
    }

    /**
     * Method for Iterator interface
     *
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function rewind() 
    {
        return $this->ps()->itemsIterator->rewind();
    }

    /**
     * Method for Iterator interface
     *
     * @return boolean
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function valid() 
    {
        return $this->ps()->itemsIterator->valid();
    }

    /**
     * Method for Iterator interface
     *
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function next() 
    {
        return $this->ps()->itemsIterator->next();
    }

    /**
     * Method for Iterator interface
     *
     * @return scalar
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function key() 
    {
        return $this->ps()->itemsIterator->key();
    }

    /**
     * Method for Iterator interface
     *
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function current() 
    {
        return $this->ps()->itemsIterator->current();
    }

    /**
     * Convert array-iterator to a new array
     *
     * @return array
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function getArrayCopy() 
    {
        return $this->ps()->itemsIterator->getArrayCopy();
    }

    /**
     * Check the existence of property
     * 
     * @param mixed Key
     * @return boolean
     * @see http://php.net/manual/en/language.oop5.magic.php
     * @throws FaZend_Pos_Exception If something goes wrong with the object
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
     * @see http://php.net/manual/en/language.oop5.magic.php
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function __unset($name)
    {
        return $this->ps()->unsetProperty($name);
    }
    
    /**
     * Called after unserialize(), magic method
     *
     * Here we should reload the object from DB and fill it's internal
     * structure with data.
     *
     * @return void
     * @see http://php.net/manual/en/language.oop5.magic.php
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function __wakeup() 
    {
        if ($this instanceof FaZend_Pos_Root) {
            FaZend_Exception::raise('FaZend_Pos_RootUnserializationProhibited',
                "Object of class " . get_class($this) . " can't be unserialized, since it's ROOT",
                'FaZend_Pos_Exception');
        }

        if (!isset($this->__posId) || !$this->__posId) {
            FaZend_Exception::raise('FaZend_Pos_UnserializationFailure',
                "Object of class " . get_class($this) . " wasn't properly serialized",
                'FaZend_Pos_Exception');
        }

        // The only thing we know about the object is its ID (fzObject.id).
        // Now we should recover its "parent", in order to make it attached
        // to the POS structure. This operation will be done recursively, until
        // the ROOT is reached.
        $this->ps()->recoverById($this->__posId);
        
    }

    /**
     * Called before serialize()
     *
     * @return void
     * @see http://php.net/manual/en/language.oop5.magic.php
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     **/
    public function __sleep() 
    {
        if ($this instanceof FaZend_Pos_Root) {
            FaZend_Exception::raise('FaZend_Pos_RootSerializationProhibited',
                "Object of class " . get_class($this) . " can't be serialized, since it's ROOT",
                'FaZend_Pos_Exception');
        }

        // We should validate, maybe we already serialized this object before?
        if (is_null($this->__posId)) {
            // We're trying to save the object. There could be an error, if the
            // object is NOT yet in POS.
            try {
                $this->__posId = $this->ps()->id;
                $this->ps()->save();
            } catch (FaZend_Pos_LostObjectException $e) {
                FaZend_Exception::raise('FaZend_Pos_SerializationProhibited',
                    "Object of class " . get_class($this) . " can't be serialized, since it's not in POS",
                    'FaZend_Pos_Exception');
            } 
        }
        
        return array('__posId');
    }

}
