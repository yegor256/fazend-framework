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
 * Wrapper around tables, adapters etc. 
 *
 * Usase sample:
 *
 * <code>
 * $rowset = FaZend_Db_ActiveTable_user::retrieve()
 *    ->where('email = ?', 'me@example.com')
 *    ->fetchAll();
 * </code>
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 */
class FaZend_Db_Wrapper {

    /**
     * Table
     *
     * @var Zend_Db_Table|string
     */
    private $_table;

    /**
     * Select
     *
     * @var Zend_Db_Select
     */
    private $_select;

    /**
     * Keep silence and return FALSE if fetchRow doesn't find anything
     *
     * @var boolean
     */
    private $_silenceIfEmpty = false;

    /**
     * Set FROM attribute to select?
     *
     * @var boolean
     */
    private $_setFrom = true;

    /**
     * Create a select object
     *
     * @param string Name of the DB table
     * @param boolean Set FROM attribute to the select or not
     * @return void
     */
    public function __construct($table, $setFrom = true) {

        $this->_table = $table;
        $this->_setFrom = $setFrom;

    }

    /**
     * Set row class
     *
     * @return void
     */
    public function setRowClass($rowClass) {
        $this->table()->setRowClass($rowClass);
        return $this;
    }

    /**
     * Get row class
     *
     * @return void
     */
    public function getRowClass() {
        return $this->table()->getRowClass();
    }

    /**
     * Keep silence and return FALSE if fetchRow() doesn't find anything
     *
     * @return void
     */
    public function setSilenceIfEmpty($flag = true) {
        $this->_silenceIfEmpty = $flag;
        return $this;
    }

    /**
     * Keep silence and return FALSE if fetchRow() doesn't find anything
     *
     * @return boolean
     */
    public function getSilenceIfEmpty() {
        return $this->_silenceIfEmpty;
    }

    /**
     * Return the table
     *
     * @return void
     */
    public function table() {

        // if we just initialized the class with constructor
        if (is_string($this->_table)) {
            $this->_table = FaZend_Db_ActiveTable::createTableClass($this->_table);
        }    

        return $this->_table;
    }

    /**
     * Return the select object (on-fly)
     *
     * @return void
     */
    public function select() {

        if (!isset($this->_select)) {
            
            $this->_select = $this->table()->select();

            $this->_select->setIntegrityCheck(false);

            if ($this->_setFrom)    
                $this->_select->from($this->table()->info(Zend_Db_Table_Abstract::NAME));
        }    

        return $this->_select;
    }

    /**
     * Fetch one row
     *
     * @return void
     * @throws FaZend_Db_Table_NotFoundException
     */
    public function fetchRow() {

        // get row with fetchRow from the select we have
        $row = call_user_func(array($this->table(), 'fetchRow'), $this->select());

        // if we should keep silence - just return what we got
        if ($this->getSilenceIfEmpty() && !$row)
            return $row;

        // if the result is OK - just return it
        if ($row)
            return $row;

        // we should create this class in any case - no matter whether
        // we throw the exception or not. because the try{}catch block
        // will expect this class and will fail to load it    
        $exceptionClassName = $this->getRowClass() . '_NotFoundException';

        // raise this exception
        FaZend_Exception::raise($exceptionClassName, 
            'row not found in ' . $this->getRowClass(),
            'FaZend_Db_Table_NotFoundException');

    }

    /**
     * Fetch all wrapper
     *
     * @return FaZend_Db_RowsetWrapper
     */
    public function fetchAll() {

        return new FaZend_Db_RowsetWrapper($this->table(), $this->select());

    }

    /**
     * Call wrapping, all other functions will go directly to SELECT object
     *
     * @return FaZend_Db_Wrapper
     */
    public function __call($name, $args) {

        $this->_select = call_user_func_array(array($this->select(), $name), $args);
        return $this;

    }

}
