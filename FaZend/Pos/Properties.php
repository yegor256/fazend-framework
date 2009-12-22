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
 * Dynamic properties for any POS object
 * 
 * @package Pos
 */
class FaZend_Pos_Properties
{
    
    // By means of this prefix we distinguish properties
    // and array items. Physically they are stored in the same
    // array: $this->_properties. But array items have this prefix
    // in their names.
    const ARRAY_PREFIX = 'item:';

    // This class will be assigned to $this->_properties instead
    // of partOf relations, in order to indicate that the relation
    // exists, but is not loaded yet.
    const STUB_CLASS = 'FaZend_Pos_StubClass';
    
    /**
     * Current user ID
     *
     * @var integer
     **/
    protected static $_userId = null;

    /**
     * Object that we're managing
     * 
     * @var FaZend_Pos_Model_Object
     */
    protected $_fzObject = null;
    
    /**
     * Object that we're managing
     * 
     * @var FaZend_Pos_Abstract
     */
    protected $_object = null;
    
    /**
     * Latest snapshot
     * 
     * @var FaZend_Pos_Model_Snapshot
     */
    protected $_fzSnapshot = null;
    
    /**
     * Parent object
     *
     * @var FaZend_Pos_Abstract
     **/
    protected $_parent = null;

    /**
     * Does it have any unsaved changes?
     * 
     * NULL means that the object is NOT attached to POS. And we can't
     * make any changes to it.
     *
     * @var boolean TRUE if it's clean, FALSE = some changes were made
     */
    protected $_clean = null;

    /**
     * List of object properties
     * 
     * @var ArrayIterator Defaults to array(). 
     */
    protected $_properties;
    
    /**
     * Iterator through properties, showing ONLY items
     *
     * @var Iterator
     **/
    protected $_itemsIterator;

    /**
     * Stores the version associated with this object.  By default, this will
     * always be null, indicating the current version.  Only when the user calls
     * a function which forces a version will this have a value.
     * 
     * @var int Defaults to null. 
     */
    protected $_version = null;

    /**
     * Set user ID, dependency injection, so to speak
     *
     * @param integer User ID to use later
     * @return void
     **/
    public static function setUserId($userId) 
    {
        self::$_userId = $userId;
    }
    
    /**
     * Constructor
     *
     * @param FaZend_Pos_Abstract Object
     * @return void
     **/
    public function __construct(FaZend_Pos_Abstract $object) 
    {
        $this->_object = $object;
        $this->_properties = new ArrayIterator();
    }

    /**
     * Save all changes to DB
     *
     * @return void
     **/
    public function __destruct() 
    {
        $this->save();
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string
     **/
    public function __get($name) {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
        
        FaZend_Exception::raise('FaZend_Pos_Properties_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this),
            'FaZend_Pos_Exception');        
    }
    
    /**
     * Setter dispatcher
     *
     * @param string Name of property to set
     * @param mixed Value to set
     * @return void
     **/
    public function __set($name, $value) {
        $method = '_set' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method($value);
        
        FaZend_Exception::raise('FaZend_Pos_Properties_PropertyOrMethodNotFound', 
            "Can't set '$name' in " . get_class($this),
            'FaZend_Pos_Exception');        
    }
    
    /**
     * Set one property
     *
     * @param string Name of the property
     * @param mixed Value of it
     * @return void
     **/
    public function setProperty($name, $value) 
    {
        $this->_attachToPos();
        
        $this->_resolveStub($name);
        $this->_properties[$name] = $value;
        
        // this flag will be validated later, in _saveSnapshot()        
        $this->_clean = false;

        // this new property is also a POS object?
        if ($value instanceof FaZend_Pos_Abstract) {
            $value->ps()->_attachTo($this->_object, $name);
        }
    }

    /**
     * Get one property
     *
     * @param string Name of the property
     * @return mixed
     **/
    public function getProperty($name) 
    {
        $this->_attachToPos();

        if (!$this->hasProperty($name))
            FaZend_Exception::raise('FaZend_Pos_Properties_PropertyMissed', 
                "Can't find property '{$name}' in " . get_class($this->_object),
                'FaZend_Pos_Exception');        
                
        $this->_resolveStub($name);
        return $this->_properties[$name];
    }

    /**
     * Has this property?
     *
     * @param string Name of the property
     * @return boolean
     **/
    public function hasProperty($name) 
    {
        $this->_attachToPos();
        return array_key_exists($name, $this->_properties);
    }

    /**
     * Remove the property
     *
     * @param string Name of the property
     * @return void
     **/
    public function unsetProperty($name) 
    {
        $this->_attachToPos();

        if (!$this->hasProperty($name))
            FaZend_Exception::raise('FaZend_Pos_Properties_PropertyMissed', 
                "Can't find property '{$name}' in " . get_class($this),
                'FaZend_Pos_Exception');        
        
        // this flag will be validated later, in _saveSnapshot()        
        $this->_clean = false;
        
        unset($this->_properties[$name]);
    }

    /**
     * Set one item
     *
     * @param string|null Name of the item or null
     * @param mixed Value of it
     * @return void
     **/
    public function setItem($name, $value) 
    {
        if ($name === null) {
            $keys = array_map(
                create_function('$a', 'return substr($a, ' . strlen(self::ARRAY_PREFIX) . ');'),
                array_keys($this->itemsIterator->getArrayCopy()));
            if (count($keys))
                $name = max($keys) + 1;
            else
                $name = 0;
        }
        $this->setProperty(self::ARRAY_PREFIX . $name, $value);
    }

    /**
     * Get one item
     *
     * @param string Name of the item
     * @return mixed
     **/
    public function getItem($name) 
    {
        $this->getProperty(self::ARRAY_PREFIX . $name);
    }

    /**
     * Has this item
     *
     * @param string Name of the property
     * @return boolean
     **/
    public function hasItem($name) 
    {
        $this->hasProperty(self::ARRAY_PREFIX . $name);
    }

    /**
     * Remove the item
     *
     * @param string Name of the item
     * @return void
     **/
    public function unsetItem($name) 
    {
        $this->unsetProperty(self::ARRAY_PREFIX . $name);
    }

    /**
     * Load this object
     *
     * @param boolean Force loading in any case?
     * @return void
     **/
    public function load($force = false) 
    {
        // object is NOT loaded from DB yet?
        if (is_null($this->_clean) || $force) {
            $this->_loadSnapshot();
            $this->_clean = true;
        }

        if ($this->_clean === false)
            FaZend_Exception::raise('FaZend_Pos_DirtyObjectException',
                "The object is dirty, you can't reload it",
                'FaZend_Pos_Exception');
    }

    /**
     * Save object
     *
     * @param boolean Save anyway
     * @return void
     **/
    public function save($force = false) 
    {
        if (is_null($this->_parent))
            return;
            
        // object is NOT saved to DB yet?
        if (($this->_clean === false) || $force) {
            $this->_saveSnapshot();
            $this->_clean = true;
        }
    }
    
    /**
     * Increate version
     *
     * @return void
     **/
    public function touch() 
    {
        $this->save(true);
    }

    /**
     * Validate whether the object is already in POS
     *
     * @return void
     **/
    protected function _attachToPos() 
    {
        // parent is not assigned yet? no access is allowed
        if (is_null($this->_parent))
            FaZend_Exception::raise('FaZend_Pos_LostObjectException',
                "You can't make changes to the object since it's not in POS yet",
                'FaZend_Pos_Exception');
        // the object was never loaded yet
        if (is_null($this->_clean))
            $this->load();
    }

    /**
     * Set parent for the object
     * 
     * This method will be called ONLY by this class. No one else can
     * change parent of the object.
     *
     * @param FaZend_Pos_Abstract The object, which is parent
     * @param string Unique name inside the parent
     * @return void
     **/
    protected function _attachTo(FaZend_Pos_Abstract $parent, $name) 
    {
        $this->_parent = $parent;
        
        try {
            // find my ID
            $this->_fzObject = FaZend_Pos_Model_Object::findByParent($parent, $name);
        } catch (FaZend_Pos_Model_Object_NotFoundException $e) {
            $this->_fzObject = FaZend_Pos_Model_Object::create($this->_object, $parent, $name);
        }
        
        // make sure it is property attached
        $this->_attachToPos();
    }
    
    /**
     * Get object latest editor
     * 
     * @return FaZend_User 
     */
    protected function _getEditor()
    {
        $this->_attachToPos();
        return $this->_fzSnapshot->user;
    }

    /**
     * Get object latest version number
     * 
     * @return integer
     */
    protected function _getVersion()
    {
        $this->_attachToPos();
        return intval($this->_fzSnapshot->version);
    }

    /**
     * Date of latest modificsation
     * 
     * @return Zend_Date
     */
    protected function _getUpdated()
    {
        $this->_attachToPos();
        return new Zend_Date($this->_fzSnapshot->updated);
    }

    /**
     * ID of the object
     * 
     * @return integer
     */
    protected function _getId()
    {
        $this->_attachToPos();
        return intval((string)$this->_fzObject);
    }

    /**
     * List of all properties
     *
     * @return string[]
     */
    protected function _getProperties()
    {
        $this->_attachToPos();
        return array_keys($this->_properties->getArrayCopy());
    }

    /**
     * Get iterator for the items
     *
     * @return ArrayIterator
     */
    protected function _getItemsIterator()
    {
        $this->_attachToPos();
        if (!isset($this->_itemsIterator)) {
            $this->_itemsIterator = new RegexIterator(
                $this->_properties,
                '/^' . preg_quote(self::ARRAY_PREFIX) . '/',
                RegexIterator::MATCH, 
                RegexIterator::USE_KEY);
        }
        return $this->_itemsIterator;
    }

    /**
     * fzObject row
     * 
     * @return FaZend_Pos_Model_Object
     */
    protected function _getFzObject()
    {
        $this->_attachToPos();
        return $this->_fzObject;
    }

    /**
     * Get type of object (PHP class name)
     * 
     * @return string
     */
    protected function _getType()
    {
        $this->_attachToPos();
        return $this->_fzObject->class;
    }

    /**
     * Get parent
     * 
     * @return FaZend_Pos_Abstract
     */
    protected function _getParent()
    {
        $this->_attachToPos();
        return $this->_parent;
    }

    /**
     * Write a new snapshot to the database
     * 
     * @return void 
     */
    private function _saveSnapshot()
    {
        $toSerialize = array();
        foreach ($this->_properties as $key=>$property) {
            // don't touch the stubs
            $stubClass = self::STUB_CLASS;
            if ($property instanceof $stubClass)
                continue;
                
            if ($property instanceof FaZend_Pos_Abstract) {
                try {
                    FaZend_Pos_Model_PartOf::findByParent($this->_fzObject, $key);
                } catch (FaZend_Pos_Model_PartOf_NotFoundException $e) {
                    FaZend_Pos_Model_PartOf::create($property->ps()->fzObject, $this->_fzObject, $key);
                }
            } else {
                $toSerialize[$key] = $property;
            }
        }
        
        $this->_fzSnapshot = FaZend_Pos_Model_Snapshot::create(
            $this->_fzObject, 
            self::$_userId, 
            serialize($toSerialize));
    }
    
    /**
     * Loads a snapshot, from the DB
     *
     * @return void
     */
    private function _loadSnapshot() 
    {
        try {
            $this->_fzSnapshot = FaZend_Pos_Model_Snapshot::findByObject($this->_fzObject);
        } catch (FaZend_Pos_Model_Snapshot_NotFoundException $e) {
            $this->_fzSnapshot = FaZend_Pos_Model_Snapshot::create($this->_fzObject, self::$_userId, serialize(array()));
        }
        $this->_properties = new ArrayIterator(unserialize($this->_fzSnapshot->properties));
        
        foreach (FaZend_Pos_Model_PartOf::retrieveByParent($this->_fzObject) as $partOf) {
            $this->_properties[$partOf->name] = $this->_restoreFromObject(
                $partOf->getObject('kid', 'FaZend_Pos_Model_Object'));
        }
    }

    /**
     * Restore object from fzObject
     *
     * @param FaZend_Pos_Model_Object
     * @param string Name of the kid
     * @return FaZend_Pos_Abstract
     **/
    private function _restoreFromObject(FaZend_Pos_Model_Object $fzObject) 
    {
        $stub = self::STUB_CLASS;
        if (!class_exists($stub, false))
            eval("class {$stub} {};");
        $obj = new $stub();
        $obj->className = $fzObject->class;
        return $obj;
    }
    
    /**
     * Resolve stub the the given name, in $this->_properties
     *
     * @param strig Name of the key
     * @return void
     **/
    private function _resolveStub($name) 
    {
        if (!isset($this->_properties[$name]))
            return;
            
        $property = $this->_properties[$name];
        // this is just a stub for now and the real object should be loaded?
        $stubClass = self::STUB_CLASS;
        if ($property instanceof $stubClass) {
            $class = $property->className;
            $property = new $class();
            $property->ps()->_attachTo($this->_object, $name);
            $this->_properties[$name] = $property;
        }
    }

}
