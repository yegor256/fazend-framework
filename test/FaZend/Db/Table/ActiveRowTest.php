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

$adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
$adapter->query(
	'create table Owner (
		id integer not null primary key autoincrement, 
		name varchar(50) not null)');

$adapter->query(
	'create table Product (
		id integer not null primary key autoincrement, 
		text varchar(1024) not null, 
		owner integer not null constraint fk_product_owner references owner(id))');

$adapter->query(
	'insert into Owner values (132, "john smith")');

$adapter->query(
	'insert into Product values (10, "car", 132)');

// ORM auto-mapping classes
class Owner extends FaZend_Db_Table_ActiveRow_Owner {}
class Product extends FaZend_Db_Table_ActiveRow_Product {}

class FaZend_Db_Table_ActiveRowTest extends AbstractTestCase {
	
	public function testCreationWorks () {

		$owner = new Owner(132);

		$product = new Product();
		$product->text = 'just test';
		$product->owner = $owner;
		$product->save();

	}

	public function testGettingWorks () {

		$product = new Product(10);
		$name = $product->owner->name;
		echo "Owner: {$product->owner}, Name: {$name}";

	}

	public function testRetrieveWorks () {

		$list = Owner::retrieve()
			->where('name is not null')
			->fetchAll();

	}

}
