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
         * Creates a table in a factory
         *
         * @return void
         */
	public static function createTable($config) {
		if (isset (self::$_tables[]))
			throw new Exception("Table $name already defined");

		self::$_tables[] = new FaZend_Db_Table($config);
	}

        /**
         * Returns a table
         *
         * @return FaZend_Db_Table
         */
	public static function get($name) {
		return self::$_tables[$name];
	}

}
