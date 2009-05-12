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
 * Creates nice table and fast
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 */
class FaZend_DbFactory {

        /**
         * Collection of tables
         *
         * @var array
         */
        private static $_tables = array();

        /**
         * Collection of row->table mapping
         *
         * @var array
         */
        private static $_rowToTableMapping = array();

        /**
         * Creates a table in a factory
         *
         * @return void
         */
	public static function create($config) {
		if (isset(self::$_tables[$config['name']]))
			throw new Exception("Table {$config['name']} already defined");
			
		self::$_tables[$config['name']] = new FaZend_Db_Table($config);

		if (!isset(self::$_rowToTableMapping[$config['rowClass']]))
			self::$_rowToTableMapping[$config['rowClass']] = $config['name'];
	}

        /**
         * Returns a table
         *
         * @return FaZend_Db_Table
         */
	public static function get($name) {
		return self::$_tables[$name];
	}

        /**
         * Returns a table
         *
         * @return FaZend_Db_Table
         */
	public static function getForRow($rowName) {

		if (!isset(self::$_rowToTableMapping[$rowName]))
			throw new Exception("table for row $rowName is not defined in app.ini");

		return self::$_tables[self::$_rowToTableMapping[$rowName]];
	}

}
