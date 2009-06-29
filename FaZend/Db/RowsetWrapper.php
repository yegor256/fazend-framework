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
 * Wrapper for fetchAll method
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 */
class FaZend_Db_RowsetWrapper implements SeekableIterator, Countable, ArrayAccess {

    /**
     * Table
     *
     * @var Zend_Db_Table
     */
    private $_table;

    /**
     * Select
     *
     * @var Zend_Db_Select
     */
    private $_select;

    /**
     * Rowset
     *
     * @var Zend_Db_Table_Rowset
     */
    private $_rowset;

    /**
     * Create a rowset wrapping object
     *
     * @param Zend_Db_Table
     * @param Zend_Db_Select
     * @return void
     */
    public function __construct(Zend_Db_Table $table, Zend_Db_Select $select) {

        $this->_table = $table;
        $this->_select = $select;

    }

    /**
     * Returns select object
     *
     * @return Zend_Db_Select
     */
    public function select() {

        return $this->_select;

    }

    /**
     * Count objects in the rowset
     *
     * @return int
     */
    public function count() {

        return $this->_table->getAdapter()->fetchOne('SELECT COUNT(*) FROM (' . (string)$this->_select . ') AS tbl');

    }

    /**
     * Call wrapping, all calls will be forwarded to rowset
     *
     * If the rowset is not used yet, it will be created right now
     *
     * @return void
     */
    public function __call($name, $args) {

        if (!isset($this->_rowset))
            $this->_rowset = $this->_table->fetchAll($this->_select);

        return call_user_func_array(array($this->_rowset, $name), $args);

    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function seek($position) {
        return $this->__call('seek', array($position));
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function key() {
        return $this->__call('key', array());
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function next() {
        return $this->__call('next', array());
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function current() {
        return $this->__call('current', array());
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function valid() {
        return $this->__call('valid', array());
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function rewind() {
        return $this->__call('rewind', array());
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function offsetExists($offset) {
        return $this->__call('offsetExists', array($offset));
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function offsetGet($offset) {
        return $this->__call('offsetGet', array($offset));
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function offsetSet($offset, $value) {
        return $this->__call('offsetSet', array($offset, $value));
    }

    /**
     * Method wrapping
     *
     * @return 
     */
    public function offsetUnset($offset) {
        return $this->__call('offsetUnset', array($offset));
    }

}
