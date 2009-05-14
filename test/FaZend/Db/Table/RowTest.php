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

require_once 'AbstractTestCase.php';

class Table_User extends FaZend_Db_Table_Row {
	public static function findById($id) {
		return self::_findById($id);
	}
	public static function retrieve() {
		return self::_retrieve();
	}

	public static function create() {
		$table = self::_getTable();
		return $table->insert(array(
			'id' => 0,
			'email' => 'test@fazend.com',
			'password' => 'test'
		));
	}
	public function getTable() {
		$table = $this->_getTable();
		return $table;
	}
}

class FaZend_Db_Table_RowTest extends AbstractTestCase {
	
	public function testFindByIdWorks () {

		$user = Table_User::findById(0);

	}

	public function testRetrieveWorks () {

		$list = Table_User::retrieve();

	}

	public function testGetTableWorks () {

		Table_User::create();
		$user = Table_User::findById(0);
		$table = $user->getTable();

	}

}
