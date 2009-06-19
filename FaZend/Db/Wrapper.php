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
 * Simple table
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 */
class FaZend_Db_Wrapper {

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
	 * Keep silence and return FALSE if fetchRow doesn't find anything
	 *
	 * @var boolean
	 */
	private $_silenceIfEmpty;

        /**
         * Create a select object
         *
         * @return void
         */
	public function __construct($table, $setFrom = true) {

		$tableClassName = 'FaZend_Db_ActiveTable_' . $table;

		$this->_table = new $tableClassName();

		$this->_select = $this->_table->select()
			->setIntegrityCheck(false);

		if ($setFrom)	
			$this->_select->from($table);
	}

        /**
         * Set row class
         *
         * @return void
         */
	public function setRowClass($rowClass) {
		$this->_table->setRowClass($rowClass);
		return $this;
	}

        /**
         * Get row class
         *
         * @return void
         */
	public function getRowClass() {
		return $this->_table->getRowClass();
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
        	return $this->_table;
	}

        /**
         * Return the select
         *
         * @return void
         */
	public function select() {
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
        	$row = call_user_func(array($this->_table, 'fetchRow'), $this->_select);

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
         * Call wrapping
         *
         * @return void
         */
	public function __call($name, $args) {

		if (in_array($name, array('fetchAll')))
			return call_user_func(array($this->_table, $name), $this->_select);

		$this->_select = call_user_func_array(array($this->_select, $name), $args);

		return $this;

	}

}
