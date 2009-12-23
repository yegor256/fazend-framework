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
            "Can't find what is '$name' in " . get_class($this->_object) . "::ps()",
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
            "Can't set '$name' in " . get_class($this->_object) . '::ps()',
            'FaZend_Pos_Exception');        
    }
    
    /**
     * Set one property
     *
     * @param string Name of the property
     * @param mixed Value of it
     * @return void
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
     **/
    public function unsetProperty($name) 
    {
        $this->_attachToPos();

        if (!$this->hasProperty($name))
            FaZend_Exception::raise('FaZend_Pos_Properties_PropertyMissed', 
                "Can't find and unset() property '{$name}' in " . get_class($this->_object),
                'FaZend_Pos_Exception');        
        
        // this flag will be validated later, in _saveSnapshot()        
        $this->_clean = false;
        
        unset($this->_properties[$name]);
    }

    /**
     * Set one item, inside an array
     *
     * This is similar to setting a property (internally), the only difference
     * is in the name of the property. To distinguish properties from array items
     * we're using a special prefix (self::ARRAY_PREFIX).
     *
     * @param string|null Name of the item or null
     * @param mixed Value of it
     * @return void
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @see setItem()
     * @throws FaZend_Pos_Exception If the object is not in POS yet
     **/
    public function getItem($name) 
    {
        $this->getProperty(self::ARRAY_PREFIX . $name);
    }

    /**
     * Checks whether the array has the item mentioned
     *
     * @param string Name of the item
     * @return boolean The item exists in the array?
     * @see setItem()
     * @throws FaZend_Pos_Exception If the object is not in POS yet
     **/
    public function hasItem($name) 
    {
        $this->hasProperty(self::ARRAY_PREFIX . $name);
    }

    /**
     * Remove the item from the array, if it exists there
     *
     * @param string Name of the item
     * @return void
     * @see setItem()
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
     **/
    public function load($force = true) 
    {
        // object is NOT loaded from DB yet?
        if (is_null($this->_clean) || $force)
            $this->_loadSnapshot();

        if ($this->_clean === false)
            FaZend_Exception::raise('FaZend_Pos_DirtyObjectException',
                "The object of class " . get_class($this->_object) . " is dirty, you can't reload it",
                'FaZend_Pos_Exception');
        
        // it's always CLEAN after loading
        $this->_clean = true;
    }

    /**
     * Save object
     *
     * @param boolean Save anyway
     * @return void
     * @throws FaZend_Pos_Exception If the object is not in POS yet
     **/
    public function save($force = true) 
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
     **/
    public function touch() 
    {
        $this->save(true);
    }

    /**
     * Recovers the object by fzObject.id
     * 
     * This method is called only from FaZend_Pos_Abstract::__wakeup().
     *
     * Here we recursively (!) restore all parents, until root is reached.
     *
     * @param id fzObject.id
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong
     * @see FaZend_Pos_Abstract::__wakeup()
     **/
    public function recoverById($id) 
    {
        try {
            $partOf = FaZend_Pos_Model_PartOf::findByKid(new FaZend_Pos_Model_Object(intval($id)));
        } catch (FaZend_Pos_Model_PartOf_NotFoundException $e) {
            // parent not found, we're the root!
            FaZend_Exception::raise('FaZend_Pos_RootCantBeRecovered',
                "Root object can't be recovered by ID",
                'FaZend_Pos_Exception');
        }
        
        $parentClassName = $partOf->getObject('parent', 'FaZend_Pos_Model_Object')->class;
        
        // attach it to ROOT?
        if (is_subclass_of($parentClassName, 'FaZend_Pos_Root') || ($parentClassName === 'FaZend_Pos_Root')) {
            FaZend_Pos_Abstract::root()->{$partOf->name} = $this->_object;
        } else {
            $parent = new $parentClassName();
            $parent->ps()->recoverById((string)$partOf->parent);
            $this->_attachTo($parent, $partOf->name);
        }
    }

    /**
     * Validate whether the object is already in POS
     *
     * This method is called internally from every place that is trying to
     * do something with the object. This method makes sure that the object
     * is properly attached to the POS, and has $this->_parent defined.
     *
     * Internal property $this->_parent should be set BEFOREHAND by means of
     * $this->_attachTo().
     *
     * @return void
     * @throws FaZend_Pos_LostObjectException If the object is not attached yet
     * @see _attachTo()
     **/
    protected function _attachToPos() 
    {
        // parent is not assigned yet? no access is allowed
        if (is_null($this->_parent))
            FaZend_Exception::raise('FaZend_Pos_LostObjectException',
                "You can't make changes to the object " . get_class($this->_object) . " since it's not in POS yet",
                'FaZend_Pos_Exception');
        // the object was never loaded yet
        if (is_null($this->_clean))
            $this->load(false);
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
     * @throws FaZend_Pos_Exception
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * @throws FaZend_Pos_Exception If the object is not in POS yet
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
     * Resolve stub by the given name, in $this->_properties
     *
     * When we have a link between this object and some other, we place this
     * link into $this->_properties in a form of STUB. This is not a real
     * object, but an information about what the real object should be. When
     * it's time to "resolve" this stub, we replace it with the real object,
     * recreating it from the class name, that we stored in the STUB before.
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
