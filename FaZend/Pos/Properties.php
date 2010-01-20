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
     * Root object
     *
     * @var FaZend_Pos_Abstract
     **/
    protected static $_root = null;
    
    /**
     * Name of root class
     *
     * @var string
     **/
    protected static $_rootClass = 'FaZend_Pos_Root';
   
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
     * Associative array where keys are fzObject ID's and values
     * are instances of FaZend_Pos_Properties.
     *
     * @var FaZend_Pos_Properties[]
     * @see cleanPosMemory()
     * @see self::_attachTo()
     **/
    protected static $_instances = array();
    
    /**
     * Active Row in fzObject table
     * 
     * This is the row in fzObject table, that relates to the object we
     * are managing. This property is set inside self::_attachTo()
     *
     * @var FaZend_Pos_Model_Object
     * @see self::_attachTo()
     * @see __construct()
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
     * The property is set inside self::_attachTo(), and can be accessed by means
     * of _getParent(). If the property is not set, the object we're managing
     * is considered to be OUT OF the entire POS structure, and you can't
     * do anything with it. For example:
     *
     * <code>
     * class MyObject extends FaZend_Pos_Abstract {}
     * $obj = new MyObject();
     * $obj->name = 'test'; // Exception here, since the object is not attached
     * FaZend_Pos_Properties::root()->obj = $obj;
     * $obj->name = 'test'; // works perfectly
     * </code>
     *
     * @var FaZend_Pos_Abstract
     * @see self::_attachTo()
     * @see __construct()
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
     * @see isClean()
     * @see setDirty()
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
     * Shall we ignore versions?
     *
     * @var boolean
     * @see setIgnoreVersions()
     **/
    protected $_ignoreVersions = false;

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
     * @throws FaZend_Pos_RootIsLocked
     **/
    public static function setRootClass($rootClass) 
    {
        if (!is_null(self::$_root)) {
            FaZend_Exception::raise(
                'FaZend_Pos_RootIsLocked', 
                "You can't change root class when ROOT is already instantiated",
                'FaZend_Pos_Exception'
            );        
        }
        self::$_rootClass = $rootClass;
    }

    /**
     * Get root object, the main object of the entire POS tree
     *
     * @return FaZend_Pos_Abstract
     **/
    public static function root() 
    {
        if (is_null(self::$_root)) {
            self::$_root = new self::$_rootClass();
            self::$_root->init();
        }
        return self::$_root;
    }

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
     * Clean the entire POS structure from memory
     *
     * Be careful with this method, it is mostly used for unit testing. When
     * you're clearing the memory, you DON'T save changes to the database. All
     * your changes will be saved only during destruction of objects. Consider
     * the example:
     *
     * <code>
     * FaZend_Pos_Properties::root()->obj = $obj = new Model_My_Pos_Object();
     * $obj->test = 'works good!';
     * FaZend_Pos_Properties::cleanPosMemory();
     * isset(FaZend_Pos_Properties::root()->obj->test); // return FALSE
     * </code>
     *
     * Another example, which explains how it should be done:
     *
     * <code>
     * FaZend_Pos_Properties::root()->obj = $obj = new Model_My_Pos_Object();
     * $obj->test = 'works good!';
     * $obj->ps()->save(); // forces the object to be saved to DB
     * FaZend_Pos_Properties::cleanPosMemory();
     * isset(FaZend_Pos_Properties::root()->obj->test); // return TRUE
     * </code>
     *
     * Be careful with this method, since if you DON'T save to DB
     * first, you may have problems in __destruct() later. Since
     * objects will stay in memory and will be saved only when PHP
     * script is finished. Not the best place for saving, actually.
     *
     * @param boolean Save everything into DB first?
     * @param boolean Shall we ignore serialization problems?
     * @return void
     **/
    public static function cleanPosMemory($saveAll = true, $ignoreExceptions = false) 
    {
        if (is_null(self::$_root))
            return;
            
        if ($saveAll) {
            foreach (self::$_instances as $property) {
                try {
                    $property->save(false);
                } catch (FaZend_Pos_SerializationProhibited $e) {
                    if ($ignoreExceptions) {
                        FaZend_Log::err(get_class($e) . ': ' . $e->getMessage());
                    } else {
                        throw $e;
                    }
                }
            }
        }
            
        foreach (self::$_instances as $id=>$instance) {
            $instance->_object->ps(null, false, true);
            unset(self::$_instances[$id]);
        }
        self::$_root = null;
    }
    
    /**
     * Create new instance of the class
     *
     * @param FaZend_Pos_Abstract Object
     * @return FaZend_Pos_Properties
     * @see FaZend_Pos_Abstract::ps()
     * @see self::_attachTo()
     */
    public static function factory(
        $class, 
        FaZend_Pos_Abstract $object,
        FaZend_Pos_Model_Object $fzObject
    ) 
    {
        return self::$_instances[$fzObject->__id] = new $class($object, $fzObject);
    }
    
    /**
     * Constructor
     *
     * @param FaZend_Pos_Abstract Object
     * @param FaZend_Pos_Model_Object fzObject
     * @param FaZend_Pos_Abstract Parent
     * @return void
     * @see FaZend_Pos_Abstract::ps()
     **/
    private function __construct(
        FaZend_Pos_Abstract $object, 
        FaZend_Pos_Model_Object $fzObject, 
        FaZend_Pos_Abstract $parent = null
    ) 
    {
        $this->_object = $object;
        $this->_fzObject = $fzObject;
        $this->_parent = $parent;
        
        $this->_properties = new ArrayIterator();
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
            // bug(array_keys(self::$_instances));
            $this->save(false);
        } catch (FaZend_Pos_Exception $e) {
            $msg = get_class($e) . ' in ' . get_class($this) . "::__destruct: {$e->getMessage()}";
            if (defined('TESTING_RUNNING'))
                echo $msg . "\n";
            else
                logg($msg);
        }
    }

    /**
     * Getter dispatcher
     *
     * @param string Name of property to get
     * @return string
     **/
    public function __get($name)
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
        
        FaZend_Exception::raise(
            'FaZend_Pos_Properties_PropertyOrMethodNotFound', 
            "Can't find what is '$name' in " . get_class($this->_object) . "::ps()",
            'FaZend_Pos_Exception'
        );        
    }
    
    /**
     * Setter dispatcher
     *
     * @param string Name of property to set
     * @param mixed Value to set
     * @return void
     **/
    public function __set($name, $value)
    {
        $method = '_set' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method($value);
        
        FaZend_Exception::raise(
            'FaZend_Pos_Properties_PropertyOrMethodNotFound', 
            "Can't set '$name' in " . get_class($this->_object) . '::ps()',
            'FaZend_Pos_Exception'
        );        
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
     * Tells us that the object should NOT support version control
     *
     * Any change you make to the object will REMOVE the previous
     * version and replace it with the new one. Old version won't be 
     * kept in the DB.
     *
     * @return void
     **/
    public function setIgnoreVersions($ignoreVersions = true) 
    {
        $this->_ignoreVersions = $ignoreVersions;
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
        
        // this flag will be validated later, in save()        
        $this->_clean = false;

        // this new property is also a POS object?
        if ($value instanceof FaZend_Pos_Abstract) {
            self::_attachTo($this->_object, $value, $name);
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

        if (!$this->hasProperty($name)) {
            FaZend_Exception::raise(
                'FaZend_Pos_Properties_PropertyMissed', 
                sprintf(
                    "Can't find property '%s' in %s, among %d properties (%s)",
                    $name, 
                    get_class($this->_object), 
                    count($this->_properties), 
                    $this->path
                ),
                'FaZend_Pos_Exception'
            );        
        }
                
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

        if (!$this->hasProperty($name)) {
            FaZend_Exception::raise(
                'FaZend_Pos_Properties_PropertyMissed', 
                "Can't find and unset() property '{$name}' in " . get_class($this->_object) . " ({$this->path})",
                'FaZend_Pos_Exception'
            );
        }        
        
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
            $name = 0;
            foreach ($this->_properties as $key=>$item) {
                if (preg_match('/^' . preg_quote(self::ARRAY_PREFIX, '/') . '(\d+)$/', $key, $matches))
                    $name = max($name, $matches[1] + 1);
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
            FaZend_Exception::raise(
                'FaZend_Pos_Properties_ItemMissed', 
                "Can't find item [{$name}] in " . get_class($this->_object) . " ({$this->path})",
                'FaZend_Pos_Exception'
            );        
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
            FaZend_Exception::raise(
                'FaZend_Pos_Properties_ItemMissed', 
                "Can't find item [{$name}] in " . get_class($this->_object) . " ({$this->path})",
                'FaZend_Pos_Exception'
            );        
        }
    }

    /**
     * Remove all items from the array
     *
     * @return void
     * @todo Optimize it to make more fast
     **/
    public function cleanArray() 
    {
        foreach ($this->itemsIterator as $key=>$value)
            $this->unsetItem($key);
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

        if ($this->_clean === false) {
            FaZend_Exception::raise(
                'FaZend_Pos_DirtyObjectException',
                "The object of class " . get_class($this->_object) . " is dirty, you can't reload it",
                'FaZend_Pos_Exception'
            );
        }
        
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
        // if we're OUT of POS DB structure - ignore the procedure
        if (!$this->_isInPos())
            return;

        // object is NOT saved to DB yet?
        if (($this->_clean === false) || $force)
            $this->_saveSnapshot();
        $this->_clean = true;
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
        "Path: {$this->path}\n" .
        "Clean status: " . (is_null($this->_clean) ? 'NULL' : ($this->_clean ? 'TRUE' : 'FALSE')) . "\n";
        
        $text .= "fzObject:\n" . (isset($this->_fzObject) ? 
            '    id: ' . $this->_fzObject->__id . "\n    class: " . $this->_fzObject->class
            : '    NULL') . "\n";
        
        $text .= "fzSnapshot:\n" . (isset($this->_fzSnapshot) ? 
            "    id: " . $this->_fzSnapshot->__id . 
            "\n    properties (" . strlen($this->_fzSnapshot->properties) . "bytes): " . 
                cutLongLine($this->_fzSnapshot->properties, 90) .
            "\n    version: " . $this->_fzSnapshot->version .
                ($this->_ignoreVersions ? ' (version control suppressed)' : false) .
            "\n    updated: " . $this->_fzSnapshot->updated .
            "\n    editor: " . $this->_fzSnapshot->user
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
     * Is it clean, no changes have been made so far?
     *
     * @return boolean
     **/
    public function isClean() 
    {
        return $this->_clean === true;
    }

    /**
     * Set clean flag to FALSE
     *
     * @return void
     **/
    public function setDirty() 
    {
        $this->_attachToPos();
        $this->_clean = false;
    }

    /**
     * Recovers the object by fzObject.id
     * 
     * This method is called only from FaZend_Pos_Abstract::__wakeup().
     *
     * Here we recursively (!) restore all parents, until root is reached.
     *
     * @param FaZend_Pos_Abstract The object to recover
     * @param id fzObject.id
     * @return void
     * @throws FaZend_Pos_Exception If something goes wrong
     * @see FaZend_Pos_Abstract::__wakeup()
     **/
    public static function recoverById(FaZend_Pos_Abstract $object, $id) 
    {
        try {
            $partOf = FaZend_Pos_Model_PartOf::findByKid(new FaZend_Pos_Model_Object(intval($id)));
        } catch (FaZend_Pos_Model_PartOf_NotFoundException $e) {
            // parent not found, we're the root!
            FaZend_Exception::raise(
                'FaZend_Pos_RootCantBeRecovered',
                "Root object can't be recovered by ID ($id)",
                'FaZend_Pos_Exception'
            );
        }
        
        $parentClassName = $partOf->parent->class;
        
        // attach it to ROOT?
        if (is_subclass_of($parentClassName, 'FaZend_Pos_Root') || ($parentClassName === 'FaZend_Pos_Root')) {
            FaZend_Pos_Properties::root()->{$partOf->name} = $object;
        } else {
            $parent = new $parentClassName();
            self::recoverById($parent, (string)$partOf->parent);
            self::_attachTo($parent, $object, $partOf->name);
        }
    }

    /**
     * This object is in POS?
     *
     * @return boolean
     * @see _attachToPos()
     */
    protected function _isInPos() 
    {
        return !is_null($this->_parent);
    }

    /**
     * Validate whether the object is already in POS
     *
     * This method is called internally from every place that is trying to
     * do something with the object. This method makes sure that the object
     * is properly attached to the POS, and has $this->_parent defined.
     *
     * Internal property $this->_parent should be set BEFOREHAND by means of
     * self::_attachTo().
     *
     * @return void
     * @throws FaZend_Pos_LostObjectException If the object is not attached yet
     * @see _attachTo()
     **/
    protected function _attachToPos() 
    {
        // parent is not assigned yet? no access is allowed
        if (!$this->_isInPos()) {
            FaZend_Exception::raise(
                'FaZend_Pos_LostObjectException',
                "You can't make changes to the object " . get_class($this->_object) . " since it's not in POS yet",
                'FaZend_Pos_Exception'
            );
        }
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
     * @param FaZend_Pos_Abstract The object, which is a kid
     * @param string Unique name inside the parent
     * @return void
     * @throws FaZend_Pos_Exception
     **/
    protected static function _attachTo(FaZend_Pos_Abstract $parent, FaZend_Pos_Abstract $kid, $name) 
    {
        // maybe this KID object is already in POS,
        // but somewhere else? we should save it then.
        if ($kid->ps(null, false) instanceof FaZend_Pos_Properties)
            $parent->ps()->save();
            
        try {
            // find my ID
            $fzObject = FaZend_Pos_Model_Object::findByParent($parent, $name);
        } catch (FaZend_Pos_Model_Object_NotFoundException $e) {
            $fzObject = FaZend_Pos_Model_Object::create($kid);
        }
        
        // Maybe we found an object, which already exists in memory? This is
        // the ID of the object in fzObject, and we should search the list
        // of existing instances of PROPERTIES for it.
        $id = intval($fzObject->__id);

        // If it exists, we REPLACE the current one by existing one, in the object.
        if (isset(self::$_instances[$id])) {
            $kid->ps(self::$_instances[$id]);
        } else {
            self::$_instances[$id] = new self($kid, $fzObject, $parent);
            $kid->ps(self::$_instances[$id]);
            // make sure it is property attached
            $kid->ps()->_attachToPos();
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
     * Get name of the object in the current parent
     * 
     * @return string Name
     * @throws FaZend_Pos_Exception If the object is not in POS yet
     */
    protected function _getName()
    {
        $this->_attachToPos();
        try {
            $name = $this->_parent->ps()->_findKidName($this->_object);
        } catch (FaZend_Pos_KidNotFoundByObject $e) {
            FaZend_Exception::raise(
                'FaZend_Pos_Exception',
                "Very strange situation, probably some changes happened to the object"
            );
        }
        
        if (strpos($name, self::ARRAY_PREFIX) === 0)
            $name = substr($name, strlen(self::ARRAY_PREFIX));
        
        return $name;
    }

    /**
     * Find name of the given kid, inside me
     *
     * @param FaZend_Pos_Abstract Object to look for
     * @return string
     * @throws FaZend_Pos_KidNotFoundByObject
     */
    protected function _findKidName(FaZend_Pos_Abstract $kid) 
    {
        foreach ($this->_properties as $name=>$property) {
            if (($property instanceof FaZend_Pos_Abstract) && 
                ($kid->ps()->id == $property->ps()->id))
                return $name;
        }
            
        FaZend_Exception::raise(
            'FaZend_Pos_KidNotFoundByObject',
            "Kid not found"
        );
    }

    /**
     * Get full path of the object
     * 
     * @return string Full path in tree, like: 'root/test/myObject/myElement'
     * @see _getUplinks()
     * @throws FaZend_Pos_Exception If the object is not in POS yet
     */
    protected function _getPath()
    {
        $this->_attachToPos();
        
        $uplinks = $this->_getUplinks();
        $path = '';
        foreach ($uplinks as $uplink)
            $path .= $uplink->ps()->name . '/';
        return $path . $this->name;
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
        // when this bug is fixed:
        // http://bugs.php.net/bug.php?id=50579
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
        // to make sure the object ($this) is still a complete
        // object, mostly for unit tests, since in normal life
        // this will never happen (should not)
        assert($this->_fzObject instanceof FaZend_Pos_Model_Object);

        // prepare an array to be serialized and saved into snapshot
        $toSerialize = array();
        foreach ($this->_properties as $key=>$property) {
            // don't touch the stubs
            $stubClass = self::STUB_CLASS;
            if ($property instanceof $stubClass)
                continue;
                
            if ($property instanceof FaZend_Pos_Abstract) {
                // memory can be destroyed already and some
                // objects may be missed, we should catch such
                // a situation properly
                assert($property->ps()->fzObject instanceof FaZend_Pos_Model_Object);
                
                try {
                    FaZend_Pos_Model_PartOf::findByParent($this->_fzObject, $key);
                } catch (FaZend_Pos_Model_PartOf_NotFoundException $e) {
                    FaZend_Pos_Model_PartOf::create(
                        $property->ps()->fzObject, 
                        $this->_fzObject, 
                        $key
                    );
                }
                continue;
            }

            $toSerialize[$key] = $property;
        }
        
        $serialized = serialize($toSerialize);

        // avoid saving of the same data
        if ($this->_fzSnapshot->properties !== $serialized) {
            if ($this->_ignoreVersions) {
                $this->_fzSnapshot->update(self::$_userId, $serialized);
            } else {
                $this->_fzSnapshot = FaZend_Pos_Model_Snapshot::create(
                    $this->_fzObject, 
                    self::$_userId, 
                    $serialized
                );
            }
        }
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
            $this->_fzSnapshot = FaZend_Pos_Model_Snapshot::create(
                $this->_fzObject, 
                self::$_userId, 
                serialize(array())
            );
        }
        
        // there is a potential problem, if a class that is
        // serialized is actually absent in PHP now.. what shall
        // we do and how to detect this problem?
        // @todo We should resolve it somehow
        $this->_properties = new ArrayIterator(@unserialize($this->_fzSnapshot->properties));
        
        foreach (FaZend_Pos_Model_PartOf::retrieveByParent($this->_fzObject) as $partOf) {
            $this->_properties[$partOf->name] = $this->_restoreFromObject(
                $partOf->kid
            );
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
            self::_attachTo($this->_object, $property, $name);
            $this->_properties[$name] = $property;
        }
    }
    
    /**
     * Get list of objects that are ABOVE the current one
     *
     * @param array List of uplinks, already prepared
     * @return array
     */
    protected function _getUplinks(array $uplinks = array()) 
    {
        $uplink = $this->_parent->ps()->_object;
        if (isset($uplinks[$uplink->ps()->id]))
            return $uplinks;
        $uplinks[$uplink->ps()->id] = $uplink;
        $this->_parent->ps()->_getUplinks($uplinks);
        return $uplinks;
    }

}
