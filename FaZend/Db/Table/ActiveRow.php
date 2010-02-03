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

require_once 'Zend/Db/Table/Row.php';

/**
 * One row, ActiveRow pattern
 *
 * @see http://framework.zend.com/manual/en/zend.loader.autoloader.html
 * @package Db
 */
abstract class FaZend_Db_Table_ActiveRow extends Zend_Db_Table_Row
{

    /**
     * List of all tables in the db schema
     *
     * Locally cached in order to avoid 'SHOW TABLES' request to the
     * database performed by listTables()
     *
     * @return string[]
     */
    private static $_allTables;

    /**
     * Mapping of certain table columns to PHP classes
     *
     * It's an associative array, where keys are regular expressions
     * and values are class names.
     *
     * @var string
     * @see addMapping()
     */
    protected static $_mapping = array();

    /**
     * Time when the latest call has been made
     *
     * @var int
     */
    protected static $_latestCallTime = null;

    /**
     * Internal holder of ROW id, until the data array is filled
     *
     * @return int|string
     */
    private $_preliminaryKey;

    /**
     * Add new mapping
     *
     * @param string Regular expression to match, e.g. "/user\.project/"
     * @param string Class name, e.g. "Model_Entities_Project"
     * @return void
     * @see self::$_mapping
     */
    public static function addMapping($regex, $class) 
    {
        self::$_mapping[$regex] = $class;
    }

    /**
     * Create new row or load the existing one
     *
     * @param integer|false ID of the row to retrieve, otherwise creates NEW row
     * @return void
     */
    public function __construct($id = false)
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->autoload($this->_tableClass);

        // if the ID provided - find this row and save it
        if (is_integer($id)) {
            // create normal row
            parent::__construct();

            // save the id into the class
            // we don't load any data from DB at this stage, just remembering
            // the ID of the row
            // later data will be loaded, but later, in __get() method
            $this->_preliminaryKey = $id;

            // inject this object into flyweight
            FaZend_Flyweight::inject($this, $id);
            return;
        }
        
        if ($id !== false) {    
            // $id is NOT equal to FALSE
            // if it's not an array - that it's a mistake for sure
            if (!is_array($id)) {
                FaZend_Exception::raise(
                    'FaZend_Db_Table_InvalidConstructor', 
                    sprintf(
                        '%s::new(%s: %s) has incorrect param type (neither INT nor ARRAY)',
                        get_class($this),
                        $id,
                        gettype($id)
                    )
                );
            }

            // otherwise just pass through to the default constructor
            parent::__construct($id);
            return;
        }
        
        // $id is empty (equals to FALSE) and it means that we should
        // create a NEW object, from scratch
        parent::__construct();

        // get information from the table
        $info = $this->_table->info();

        // and create internal data array with empty values
        // for all columns
        $this->_data = array_fill_keys($info['cols'], null);
    }
    
    /**
     * Save the object
     *
     * @return void
     */
    public function save() 
    {
        parent::save();
        // inject this object into flyweight
        FaZend_Flyweight::inject($this, intval(strval($this)));
    }

    /**
     * Return object by the field
     *
     * @param string Name of the column
     * @param string Name of the class to be used for instantiating of this row
     * @return mixed
     * @deprecated This method will be REMOVED soon! Use class mapping instead!
     */
    public function getObject($name, $class)
    {
        // make sure the class has live data from DB
        $this->_loadLiveData();
        $value = parent::__get($name);
        
        // maybe toArray() already produced object
        if (!is_scalar($value))
            $value = intval((string)$value);

        return new $class(is_numeric($value) ? intval($value) : $value);
    }

    /**
     * Show the ROW as a string 
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->__get('__id');
    }

    /**
     * Delete the row
     *
     * @return mixed
     */
    public function delete()
    {
        // make sure the class has live data from DB
        $this->_loadLiveData();
        return parent::delete();
    }

    /**
     * Row actually exists in the database?
     *
     * @return boolean
     */
    public function exists()
    {
        try {
            // make sure the class has live data from DB
            $this->_loadLiveData();
            return true;
        } catch (FaZend_Db_Table_NotFoundException $e) {
            return false;
        }
    }

    /**
     * Create and array from the row
     *
     * This method overrides Zend_Db_Table_Row::toArray() because of ORM
     * concept we are using. With normal Zend Row you will get a plain
     * array with values. With FaZend you will get an array with plain
     * values AND objects. 
     *
     * @return array
     */
    public function toArray() 
    {
        $array = parent::toArray();
        foreach ($array as $key=>$value) {
            if ($key == 'id')
                continue;
            $array[$key] = $this->$key;
        }
        return $array;
    }

    /**
     * Before any call we have to be sure that live data are available
     *
     * @param string Name of the method being called
     * @param array List of parameters passed
     * @return mixed
     */
    public function __call($method, array $args)
    {
        // make sure the class has live data from DB
        $this->_loadLiveData();
        return parent::__call($method, $args);
    }

    /**
     * Find sub-objects by ID 
     *
     * @param string Name of the property to get
     * @return FaZend_Db_Table_Row|mixed
     */
    public function __get($name)
    {
        // you should not access ID field directly!
        if (strtolower($name) == 'id') {
            trigger_error(
                'ID should not be directly accesses in ' . get_class($this), 
                E_USER_WARNING
            );
        }

        // system field
        if (strtolower($name) == '__id') {
            $name = 'id';
        }

        // if we are interested in just ID and data are not loaded yet
        // we just return the ID, that's it
        if ($name === 'id' && isset($this->_preliminaryKey)) {
            return intval($this->_preliminaryKey);
        }

        // make sure the class has live data from DB
        $this->_loadLiveData();

        // get raw value from Zend_Db_Table_Row
        $value = parent::__get($name);
        // maybe we're getting the value for the second time, 
        // and it was already calculated before and stored
        // in toArray()
        if (!is_scalar($value) && !is_null($value)) {
            return $value;
        }

        foreach (self::$_mapping as $regex=>$class) {
            if (!preg_match($regex, $this->_table->info(Zend_Db_Table::NAME) . '.' . $name))
                continue;
            $rowClass = $class;
            if (is_array($class)) {
                eval("\$value = {$class[0]}::{$class[1]}(\$value);");
                return $value;
            }
            break;
        }
                
        // We are trying to understand what is the class to be
        // used for this column, if it is necessary
        if (!isset($rowClass)) {
            if (is_numeric($value) && $this->_isForeignKey(false, $name)) {
                // try to find this class and LOAD it if possible
                $rowClass = 'Model_' . ucfirst($name);
                if (!class_exists($rowClass)) {
                    $rowClass = 'FaZend_Db_Table_ActiveRow_' . $name;
                }
            }
        }

        // Here we do the type casting, if it is required, implementing
        // the core mechanism of ORM. Flyweight is used in order to avoid
        // instantiating of multiple PHP objects for the same row in the
        // database
        if (isset($rowClass)) {
            // return new $rowClass(is_numeric($value) ? intval($value) : $value);
            $value = FaZend_Flyweight::factory(
                $rowClass, 
                is_numeric($value) ? intval($value) : $value
            );
            
            // If the latest call has been completed later than a second ago
            if (self::$_latestCallTime < microtime(true) - 1) {
                // we should ping the DB here to avoid lost connections
                // @see http://framework.zend.com/issues/browse/ZF-9072
                Zend_Db_Table::getDefaultAdapter()->query('--');
                self::$_latestCallTime = microtime(true);
            }
        }

        return $value;
    }

    /**
     * Set sub-objects by ID 
     *
     * @param string Name of the property
     * @param mixed Value of the property to set
     * @return void
     */
    public function __set($name, $value)
    {
        // make sure the class has live data from DB
        $this->_loadLiveData();

        if ($value instanceof Zend_Db_Table_Row) {
            $value = intval(strval($value));
        }

        return parent::__set($name, $value);
    }

    /**
     * Load real data into the row
     *
     * The method is called only when the live data are really necessary
     * in the row. For as long as possible we should wait and NOT load
     * the data. Since every load of the live data means new SQL query.
     *
     * @return void
     */
    protected function _loadLiveData()
    {
        // if the class data are not loaded yet, it's a good moment to do it
        if (!isset($this->_preliminaryKey))
            return;

        // find data to fill the internal variables
        $rowset = $this->_table->find($this->_preliminaryKey);

        // if we failed to find anything with the given ID
        if (!count($rowset)) {
            // if the name of this class starts with 'FaZend' it means
            // that this row was received from retrieve() method from the table
            // without explicit notification of the RowClass. In such a case
            // we can't create an exception with class FaZend_Db_Table_tablename_NotFoundException
            // because we will end up in new table automatic creation by the table loader
            FaZend_Exception::raise(
                preg_match('/^FaZend_/', get_class($this)) ? 
                'FaZend_Db_Table_NotFoundException' : 
                get_class($this) . '_NotFoundException', // exception class name
                get_class($this) . " not found (ID: {$this->_preliminaryKey})", // description of the exception
                'FaZend_Db_Table_NotFoundException'  // parent class of the exception
            );
        }

        // if we found something  fill the data inside this class
        // and stop on it
        $this->_data = $this->_cleanData = $rowset->current()->toArray();

        // kill this variable, since we have LIVE data in the class already
        unset($this->_preliminaryKey);
    }

    /**
     * Does this column may be a link to subtable?
     *
     * @param string Name of the table, temporarily NOT used
     * @param string Name of the column
     * @return boolean
     */
    protected function _isForeignKey($table, $column)
    {
        // if the array of ALL tables in the db is NOT already defined
        // we should grab it from the DB by SQL request
        if (!isset(self::$_allTables))
            self::$_allTables = Zend_Db_Table_Abstract::getDefaultAdapter()->listTables();

        // whether this table is in the DB or not?
        return in_array($column, self::$_allTables);
    }

}
