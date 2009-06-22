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

$adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
$adapter->query(
	'create table owner (
		id integer not null primary key autoincrement, 
		name varchar(50) not null)');

$adapter->query(
	'create table product (
		id integer not null primary key autoincrement, 
		text varchar(1024) not null, 
		owner integer not null constraint fk_product_owner references owner(id))');

$adapter->query(
	'insert into owner values (132, "john smith")');

$adapter->query(
	'insert into product values (10, "car", 132)');

$adapter->query(
	'create table car (
		name varchar(50) not null primary key, 
		mark varchar(50))');

$adapter->query(
	'create table boat (
		id integer not null, 
		name varchar(50) not null, 
		mark varchar(50))');

