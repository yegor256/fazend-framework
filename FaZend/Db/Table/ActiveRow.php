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
 * One row
 *
 * @see http://framework.zend.com/manual/en/zend.loader.autoloader.html
 */
abstract class FaZend_Db_Table_ActiveRow extends Zend_Db_Table_Row {

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
     * Internal holder of ROW id, until the data array is filled
     *
     * @return int|string
     */
    private $_preliminaryKey;

    /**
     * Create new row or load the existing one
     *
     * @return FaZend_Db_Table_Row
     */
    public function __construct($id = false) {

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

        } elseif ($id !== false) {    

            // $id is NOT equal to FALSE
            // if it's not an array - that it's a mistake for sure
            if (!is_array($id))
                FaZend_Exception::raise('FaZend_Db_Table_InvalidConstructor', 
                    get_class($this)."::new() has incorrect param type (neither INT nor ARRAY)");

            // otherwise just pass through to the default constructor
            parent::__construct($id);

        } else {

            // $id is empty (equals to FALSE) and it means that we should
            // create a NEW object, from scratch
            parent::__construct();

            // get information from the table
            $info = $this->_table->info();

            // and create internal data array with empty values
            // for all columns
            $this->_data = array_fill_keys($info['cols'], null);
        }    
    }

    /**
     * Show the ROW as a string 
     *
     * @return string
     */
    public function __toString() {
        return $this->__id;
    }

    /**
     * Find sub-objects by ID 
     *
     * @return FaZend_Db_Table_Row|var
     */
    public function __get($name) {

        // you should not access ID field directly!
        if (strtolower($name) == 'id')
            trigger_error("ID should not be directly accesses", E_USER_WARNING);

        // system field
        if (strtolower($name) == '__id')
            $name = 'id';

        // if we are interested in just ID and data are not loaded yet
        // we just return the ID, that's it
        if ($name === 'id' && isset($this->_preliminaryKey))
            return (string)$this->_preliminaryKey;

        // make sure the class has live data from DB
        $this->_loadLiveData();

        $value = parent::__get($name);

        if (is_numeric($value) && $this->_isForeignKey(false, $name)) {
            
            if (class_exists('Model_'.ucfirst($name)))
                $rowClass = 'Model_'.ucfirst($name);
            else    
                $rowClass = 'FaZend_Db_Table_ActiveRow_' . $name;

            $value = new $rowClass((integer)$value);
        }    

        return $value;

    }

    /**
     * Set sub-objects by ID 
     *
     * @return void
     */
    public function __set($name, $value) {

        // make sure the class has live data from DB
        $this->_loadLiveData();

        if ($value instanceof Zend_Db_Table_Row) {
            $value = $value->__id;
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
    protected function _loadLiveData() {

        // if the class data are not loaded yet, it's a good moment to do it
        if (!isset($this->_preliminaryKey))
            return;

        // find data to fill the internal variables
        $rowset = $this->_table->find($this->_preliminaryKey);

        // if we failed to find anything with the given ID
        if (!count($rowset))
            // if the name of this class starts with 'FaZend' it means
            // that this row was received from retrieve() method from the table
            // without explicit notification of the RowClass. In such a case
            // we can't create an exception with class FaZend_Db_Table_tablename_NotFoundException
            // because we will end up in new table automatic creation by the table loader
            FaZend_Exception::raise(preg_match('/^FaZend_/', get_class($this)) ? 'FaZend_Db_Table_NotFoundException' : get_class($this) . '_NotFoundException', 
                get_class($this) . " not found (ID: {$this->_preliminaryKey})",
                'FaZend_Db_Table_NotFoundException');

        // if we found something  fill the data inside this class
        // and stop on it
        $this->_data = $this->_cleanData = $rowset->current()->toArray();

        // kill this variable, since we have LIVE data in the class already
        unset($this->_preliminaryKey);

    }

    /**
     * Return object by the field
     *
     * @return void
     */
    public function getObject($name, $class) {
        $id = $this->$name;
        return new $class((int)$id);
    }

    /**
     * Does this column may be a link to subtable?
     *
     * @param string Name of the table, temporarily NOT used
     * @param string Name of the column
     * @return boolean
     */
    public function _isForeignKey($table, $column) {
        
        // if the array of ALL tables in the db is NOT already defined
        // we should grab it from the DB by SQL request
        if (!isset(self::$_allTables))
            self::$_allTables = Zend_Db_Table_Abstract::getDefaultAdapter()->listTables();

        // whether this table is in the DB or not?
        return in_array($column, self::$_allTables);

    }

}
