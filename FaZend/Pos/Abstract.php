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
 * Abstract class for all POS objects
 * 
 * In order to use FaZend_Pos you should inherit your own classes from
 * this class and use them as you wish. The only important requirement is that
 * before you do anything with your POS objects, you should attach them to
 * POS root or to some other POS object, e.g.:
 *
 * <code>
 * FaZend_Pos_Properties::root()->obj = $obj = new Model_My_Pos_Object();
 * $obj->test = 'works good!';
 * </code>
 *
 * @package Pos
 */
abstract class FaZend_Pos_Abstract implements ArrayAccess, Countable, Iterator
{
    
    /**
     * Contains the system properties for this object
     * 
     * @var FaZend_Pos_Properties
     */
    protected $_ps = null;
    
    /**
     * Constructor, you CAN'T override it!
     *
     * If you need to setup some initial (!) behavior, you should use init()
     *
     * @return void
     * @see init()
     * @throws FaZend_Pos_Abstract_ExplicitPropertyFound
     */
    public final function __construct()
    {
        // we should detect all explicitly defined properties
        // and signal about it, since it's invalid behavior
        $rc = new ReflectionClass($this);
        foreach ($rc->getProperties() as $property) {
            if ($property->isStatic())
                continue;
            if ($property->getName() !== '_ps') {
                FaZend_Exception::raise(
                    'FaZend_Pos_Abstract_ExplicitPropertyFound',
                    "You're not allowed to explicitly declare properties in POS classes, " .
                    "since they won't be persistent. Property '{$property->getName()}' found in " .
                    get_class($this)
                );
            }
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
     * FaZend_Pos_Properties::root()->obj = $obj = new Model_My_Pos_Object();
     * $obj->test = 'works good!';
     * $obj->ps()->save(); // you're saving changes to the DB
     * $ver = $obj->ps()->version; // you get current version of the object
     * FaZend_Pos_Properties::cleanPosMemory();
     * isset(FaZend_Pos_Properties::root()->obj->test); // return TRUE
     * </code>
     *
     * Object in the DB are static, while in PHP they are dymanic. In other
     * words, you can have many PHP objects, linked to the same DB object. 
     * For example:
     *
     * <code>
     * $car = FaZend_Pos_Properties::root()->car;
     * $car2 = FaZend_Pos_Properties::root()->car;
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
     * @param boolean Throw exception if nothing found?
     * @param boolean Clean PS?
     * @return FaZend_Pos_Properties
     * @see FaZend_Pos_Properties::_attachTo()
     * @throws FaZend_Pos_LostObjectException
     */
    public final function ps(FaZend_Pos_Properties $ps = null, $throwException = true, $clean = false)
    {
        if ($ps instanceof FaZend_Pos_Properties)
            $this->_ps = $ps->id;
            
        if (is_null($this->_ps)) {
            if (!$throwException)
                return null;
                
            FaZend_Exception::raise(
                'FaZend_Pos_LostObjectException',
                sprintf(
                    'Object of class %s is not in POS, ->ps() is not accessible (spl: %s)',
                    get_class($this),
                    spl_object_hash($this)
                ),
                'FaZend_Pos_Exception'
            );
        }
        // clean it, see FaZend_Pos_Properties::cleanPosMemory()
        if ($clean) {
            unset($this->_ps);
            return null;
        }
            
        return FaZend_Pos_Properties::factoryByid($this->_ps);
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
     */
    public function count() 
    {
        return iterator_count($this->ps()->itemsIterator);
    }

    /**
     * Method for Iterator interface
     *
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function rewind() 
    {
        return $this->ps()->itemsIterator->rewind();
    }

    /**
     * Method for Iterator interface
     *
     * @return boolean
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function valid() 
    {
        return $this->ps()->itemsIterator->valid();
    }

    /**
     * Method for Iterator interface
     *
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function next() 
    {
        return $this->ps()->itemsIterator->next();
    }

    /**
     * Method for Iterator interface
     *
     * @return scalar
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function key() 
    {
        return $this->ps()->itemsIterator->key();
    }

    /**
     * Method for Iterator interface
     *
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
    public function current() 
    {
        return $this->ps()->itemsIterator->current();
    }

    /**
     * Convert array-iterator to a new array
     *
     * @return array
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     */
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
     * @throws FaZend_Pos_RootUnserializationProhibited
     * @throws FaZend_Pos_UnserializationFailure
     */
    public function __wakeup() 
    {
        if ($this instanceof FaZend_Pos_Root) {
            FaZend_Exception::raise(
                'FaZend_Pos_RootUnserializationProhibited',
                "Object of class " . get_class($this) . " can't be unserialized, since it's ROOT",
                'FaZend_Pos_Exception'
            );
        }

        if (empty($this->_ps)) {
            FaZend_Exception::raise(
                'FaZend_Pos_UnserializationFailure',
                "Object of class " . get_class($this) . " wasn't properly serialized",
                'FaZend_Pos_Exception'
            );
        }

        // The only thing we know about the object is its ID (fzObject.id).
        // Now we should recover its "parent", in order to make it attached
        // to the POS structure. This operation will be done recursively, until
        // the ROOT is reached.
        FaZend_Pos_Properties::recoverById($this, $this->_ps);
    }

    /**
     * Called before serialize()
     *
     * @return array List of properties to serialize
     * @see http://php.net/manual/en/language.oop5.magic.php
     * @throws FaZend_Pos_Exception If something goes wrong with the object
     * @throws FaZend_Pos_RootSerializationProhibited
     * @throws FaZend_Pos_SerializationProhibited
     */
    public function __sleep() 
    {
        if ($this instanceof FaZend_Pos_Root) {
            FaZend_Exception::raise(
                'FaZend_Pos_RootSerializationProhibited',
                "Object of class " . get_class($this) . " can't be serialized, since it's ROOT",
                'FaZend_Pos_Exception'
            );
        }

        // We should validate, maybe we already serialized this object before?
        if (is_null($this->_ps)) {
            // We're trying to save the object. There could be an error, if the
            // object is NOT yet in POS.
            try {
                $this->ps()->save();
            } catch (FaZend_Pos_LostObjectException $e) {
                FaZend_Exception::raise(
                    'FaZend_Pos_SerializationProhibited',
                    "Object of class " . get_class($this) 
                    . " can't be serialized, since it's not in POS, {$e->getMessage()}",
                    'FaZend_Pos_Exception'
                );
            } 
        }
        
        assert(!empty($this->_ps));
        return array('_ps');
    }
    
    /**
     * Magic method to be called after cloning
     *
     * @return void
     * @link http://php.net/manual/en/language.oop5.cloning.php
     */
    public function __clone() 
    {
        $this->_ps = null;
    }

}
