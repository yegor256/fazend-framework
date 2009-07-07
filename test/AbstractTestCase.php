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

// you should have Zend checked out from truck
// in the directory ../../zend-trunk
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(dirname(__FILE__) . '/../../zend-trunk'),
    realpath(dirname(__FILE__) . '/..'),
    get_include_path())));

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/test-application'));
define('FAZEND_PATH', realpath(dirname(__FILE__) . '/../FaZend'));

// we inherit from Zend Test Case
require_once 'FaZend/Test/TestCase.php';

class AbstractTestCase extends FaZend_Test_TestCase {

    /**
     * Specific setup for test environment
     *
     * @return void
     */
    public function setUp() {
        parent::setUp();

        $adapter = Zend_Db_Table_Abstract::getDefaultAdapter();
        $adapter->query('create table user (id integer not null primary key autoincrement, email varchar(50) not null, password varchar(50) not null)');
    }    

}
