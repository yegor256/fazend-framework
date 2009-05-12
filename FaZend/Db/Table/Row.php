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
 * Simple row
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 */
class FaZend_Db_Table_Row extends Zend_Db_Table_Row {

        protected $_table = false;
        
        /**
         * Find and return an object by its Id
         *
         * @return FaZend_Db_Table_Row
         */
	public static function findById ($id) {
		return $this->_getTable()->find($id)->current();
	}

        /**
         * Get a list of all rows
         *
         * @return Zend_Db_Table_RowSet
         */
	public static function retrieve () {
		return $this->_getTable()->fetchAll($this->_getTable()->select());
	}

        /**
         * Get table instance
         *
         * @return FaZend_Db_Table
         */
	protected function _getTable() {

		if ($this->_table === false)
			throw new Exception("you should define \$_table as protected property");

		return FaZend_DbFactory::get($this->_table);
	}

}
