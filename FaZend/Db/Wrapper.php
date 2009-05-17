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
         * Create a select object
         *
         * @return void
         */
	public function __construct($table) {

		$tableClassName = 'FaZend_Db_ActiveTable_' . $table;

		$this->_table = new $tableClassName();

		$this->_select = $this->_table->select()
			->setIntegrityCheck(false);
	}

        /**
         * Call wrapping
         *
         * @return void
         */
	public function __call($name, $args) {

		if (in_array($name, array('fetchAll', 'fetchRow')))
			return call_user_func(array($this->_table, $name), $this->_select);

		$this->_select = call_user_func_array(array($this->_select, $name), $args);

		return $this;

	}

}
