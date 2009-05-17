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
abstract class FaZend_Db_Table_ActiveRow extends Zend_Db_Table_row {

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
			parent::__construct();
			$rowset = $this->_table->find($id);

			if (!count($rowset))
				throw new Exception(get_class($this)." not found (id: $id)");

			$this->_data = $this->_cleanData = $rowset->current()->toArray();

		} elseif ($id !== false) {	

			if (!is_array($id))
				throw new Exception("new in ".get_class($this)." has incorrect param type (neither Int nor Array)");

			parent::__construct($id);
		} else {
			parent::__construct();

			$info = $this->_table->info();
			$this->_data = array_fill_keys($info['cols'], null);
		}	
	}

        /**
         * Show the ROW as a string 
         *
         * @return string
         */
	public function __toString() {
		return $this->id;
	}

        /**
         * Find sub-objects by ID 
         *
         * @return FaZend_Db_Table_Row|var
         */
	public function __get($name) {

		$value = parent::__get($name);

		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		$tables = array_map(create_function('$name', 'return strtolower($name);'), $db->listTables());

		if (is_numeric($value) && (in_array(strtolower($name), $tables))) {
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

		if ($value instanceof Zend_Db_Table_Row) {
			$value = $value->id;
		}

		return parent::__set($name, $value);

	}

}
