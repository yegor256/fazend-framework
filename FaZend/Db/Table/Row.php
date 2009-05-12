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

        /**
         * Find and return an object by its Id
         *
         * @return FaZend_Db_Table_Row
         */
	protected static function _findById ($id) {
		$calls = debug_backtrace();
		return FaZend_DbFactory::getForRow($calls[1]['class'])->find($id)->current();
	}

        /**
         * Get a list of all rows
         *
         * @return Zend_Db_Table_RowSet
         */
	protected static function _retrieve () {
		$calls = debug_backtrace();
		$class = $calls[1]['class'];

		$table = FaZend_DbFactory::getForRow($class);
		return $table->fetchAll($table->select());
	}

        /**
         * Get table instance
         *
         * @return FaZend_Db_Table
         */
	protected function _getTable() {
		if (isset($this)) {
			$class = get_class($this);
		} else {
			$calls = debug_backtrace();
			$class = $calls[1]['class'];
		}	
		return FaZend_DbFactory::getForRow($class);
	}

}
