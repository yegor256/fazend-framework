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
    // in their names. By means of using such prefix we assume
    // that NO properties might ever have a name that starts with
    // this prefix.
    const ARRAY_PREFIX = ':@i:';

    // This class will be assigned to $this->_properties instead
    // of partOf relations, in order to indicate that the relation
    // exists, but is not loaded yet.
    const STUB_CLASS = 'FaZend_Pos_StubClass';
    
    /**
     * Current user ID
     *
     * This variable is used for dependency injection. You should set
     * it inside your application (preferrably in bootstrap.php), and 
     * it should contain the ID (or email, if you like), of the user who
     * is currently making changes to the POS database.
     *
     * Normally this is INTEGER, since fzSnapshot.user is integer, but you
     * can change it. You should create your own "fzSnapshot.sql" file, 
     * and place it into deployment directory (application/deploy/database).
     * Once it is there, YOUR table will be created before the standard
     * POS table. And in your table you can set any type for this column.
     * Make sure that your table doesn't change names or types of OTHER
     * columns.
     *
     * @var integer|string
     * @see setUserId()
     **/
    protected static $_userId = null;
    
    /**
     * List of already existing objects
     *
     * @var FaZend_Pos_Properties[]
     **/
    protected static $_instances = array();
    
    /**
     * Active Row in fzObject table
     * 
     * This is the row in fzObject table, that relates to the object we
     * are managing. This property is set inside _attachTo()
     *
     * @var FaZend_Pos_Model_Object
     * @see _attachTo()
     */
    protected $_fzObject = null;
    
    /**
     * Object that we're managing
     * 
     * When this class is constructed, it gets a link to the object as
     * a parameter for the contructor.
     *
     * @var FaZend_Pos_Abstract
     * @see __construct()
     */
    protected $_object = null;
    
    /**
     * Latest snapshot of the object.
     *
     * This property is set inside _loadSnapshot()
     * 
     * @var FaZend_Pos_Model_Snapshot
     * @see _loadSnapshot()
     */
    protected $_fzSnapshot = null;
    
    /**
     * Parent object
     *
     * The property is set inside _attachTo(), and can be accessed by means
     * of _getParent(). If the property is not set, the object we're managing
     * is considered to be OUT OF the entire POS structure, and you can't
     * do anything with it. For example:
     *
     * <code>
     * class MyObject extends FaZend_Pos_Abstract {}
     * $obj = new MyObject();
     * $obj->name = 'test'; // Exception here, since the object is not attached
     * FaZend_Pos_Abstract::root()->obj = $obj;
     * $obj->name = 'test'; // works perfectly
     * </code>
     *
     * @var FaZend_Pos_Abstract
     * @see _attachTo()
     * @see _getParent()
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
     * This variable is set inside _construct(), and is changed by means
     * of setProperty() and unsetProperty(). You can retrieve by means
     * of _getProperties(), but be careful, since it contains BOTH
     * properties and array items. Read more about it in docBlock for
     * self::ARRAY_PREFIX.
     *
     * @var ArrayIterator Defaults to empty array 
     * @see setProperty()
     * @see unsetProperty()
     * @see ARRAY_PREFIX
     */
    protected $_properties = null;
    
    /**
     * Iterator through properties, showing ONLY items
     *
     * If you need an access to array items, you should use this iterator,
     * accessible by means of _getItemsIterator()
     *
     * @var Iterator
     * @see _getItemsIterator()
     **/
    protected $_itemsIterator;

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
     * Clean all memory structures
     *
     * @return void
     **/
    public static function cleanPosMemory() 
    {
        self::$_instances = array();
    }
    
    /**
     * Constructor
     *
     * @param FaZend_Pos_Abstract Object
     * @return void
     * @see FaZend_Pos_Abstract::ps()
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
     * Convert properties to string
     *
     * @return string
     **/
    public function __toString() 
    {
        return $this->dump(false);
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

        // @todo remove this line as soon as ItemsIterator is fixed!
        $this->_itemsIterator = null;
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
                "Can't find property '{$name}' in " . get_class($this->_object) . 
                    ', among ' . count($this->_properties) . ' properties',
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
        // @todo remove this line as soon as ItemsIterator is fixed!
        $this->_itemsIterator = null;
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
            $keys = array_keys($this->itemsIterator->getArrayCopy());
            if (count($keys)) {
                $name = max($keys) + 1;
            } else {
                $name = 0;
            }
        }
        return $this->setProperty(self::ARRAY_PREFIX . $name, $value);
    }

    /**
     * Get one item
     *
     * @param string Name of the item
     * @return mixed
     * @see setItem()
     * @throws FaZend_Pos_Properties_ItemMissed If the object is not in POS yet
     **/
    public function getItem($name) 
    {
        try {
            return $this->getProperty(self::ARRAY_PREFIX . $name);
        } catch (FaZend_Pos_Properties_PropertyMissed $e) {
            FaZend_Exception::raise('FaZend_Pos_Properties_ItemMissed', 
                "Can't find item [{$name}] in " . get_class($this->_object),
                'FaZend_Pos_Exception');        
        }
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
        return $this->hasProperty(self::ARRAY_PREFIX . $name);
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
        try{
            return $this->unsetProperty(self::ARRAY_PREFIX . $name);
        } catch (FaZend_Pos_Properties_PropertyMissed $e) {
            FaZend_Exception::raise('FaZend_Pos_Properties_ItemMissed', 
                "Can't find item [{$name}] in " . get_class($this->_object),
                'FaZend_Pos_Exception');        
        }
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
     * Show full details of the internal structure
     *
     * @param boolean Shall we DIE after it?
     * @return string
     **/
    public function dump($die = true) 
    {
        $text = 
        "Object: " . get_class($this->_object) . ' (SPL hash: ' . spl_object_hash($this->_object) . ")\n" .
        "Clean status: " . (is_null($this->_clean) ? 'NULL' : ($this->_clean ? 'TRUE' : 'FALSE')) . "\n";
        
        $text .= "fzObject:\n" . (isset($this->_fzObject) ? 
            '    id: ' . $this->_fzObject->__id . "\n    class: " . $this->_fzObject->class
            : '    NULL') . "\n";
        
        $text .= "fzSnapshot:\n" . (isset($this->_fzSnapshot) ? 
            "    id: " . $this->_fzSnapshot->__id . 
            "\n    properties: " . cutLongLine($this->_fzSnapshot->properties, 90) .
            "\n    version: " . $this->_fzSnapshot->version
            : '    NULL') . "\n";
        
        $text .= "Properties:\n";
        
        if (!count($this->_properties))
            $text .= "    none\n";
        else {
            foreach ($this->_properties as $name=>$property) {
                $text .= "    {$name}: ";
                $stubClass = self::STUB_CLASS;
                if ($property instanceof $stubClass)
                    $text .= 'STUB to ' . $property->className;
                elseif (is_scalar($property))
                    $text .= $property;
                else
                    $text .= get_class($property);
                $text .= "\n";
            }
        }
        
        $text .= "Parent: " . (isset($this->_parent) ? 
            (is_object($this->_parent) ? get_class($this->_parent) : $this->_parent) : 'NULL') . "\n";

        // @todo We should use ->versions here
        $text .= "Versions:\n";
        foreach (FaZend_Pos_Model_Snapshot::retrieveVersions($this->_fzObject) as $row)
            $text .= "    #{$row->version}: " . cutLongLine($row->properties, 100) . "\n";

        if (!$die)
            return $text;
        echo "\n\n" . $text . "\n\n";
        die();
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
        
        // Maybe we found an object, which already exists in memory? This is
        // the ID of the object in fzObject, and we should search the list
        // of existing instances of PROPERTIES for it.
        $id = $this->_fzObject->__id;
        
        // If it exists, we REPLACE the current one by existing one, in the object.
        if (isset(self::$_instances[$id])) {
            $this->_object->ps(self::$_instances[$id]);
        } else {
            self::$_instances[$id] = $this;
            // make sure it is property attached
            $this->_attachToPos();
        }
        
        // initialize the object after adding to POS
        self::$_instances[$id]->_object->init();
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
        if (isset($this->_itemsIterator))
            return $this->_itemsIterator;
            
        $this->_attachToPos();
        
        // this ugly code should be replaced by the iterator, below
        // but I don't know why - the iterator doesn't work
        // @see: http://stackoverflow.com/questions/1957069/how-to-work-with-regexiteratorreplace-mode
        $array = array();
        foreach ($this->_properties as $name=>$value) {
            if (!preg_match('/^' . preg_quote(self::ARRAY_PREFIX, '/') . '(.*)/', $name, $matches))
                continue;
            $array[$matches[1]] = $this->getItem($matches[1]);
        }
        return $this->_itemsIterator = new ArrayIterator($array);
            
        // return $this->_itemsIterator = new RegexIterator(
        //     $this->_properties,
        //     '/^' . preg_quote(self::ARRAY_PREFIX, '/') . '(.*)/',
        //     RegexIterator::REPLACE, 
        //     RegexIterator::USE_KEY);
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
     * The method returns STUB, not a real class. Later this stub can be resolved
     * to a real object, by means of 
     *
     * @param FaZend_Pos_Model_Object fzObject to restore from
     * @return FaZend_Pos_StubClass
     **/
    protected function _restoreFromObject(FaZend_Pos_Model_Object $fzObject) 
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
     * @see setProperty()
     * @see getProperty()
     **/
    protected function _resolveStub($name) 
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
