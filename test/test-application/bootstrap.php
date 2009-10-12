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
 * Bootstraper
 *
 * This file could be omitted
 *
 * @package application
 */
class Bootstrap extends FaZend_Application_Bootstrap_Bootstrap { 

    public function _initDbData() {
       
        $this->bootstrap('db');
        $this->bootstrap('Deployer');

        $adapter = Zend_Db_Table_Abstract::getDefaultAdapter();

        $adapter->query(
            'insert into owner values (132, "john smith")');

        $adapter->query(
            'insert into product values (10, "car", 132)');

        $adapter->query(
            'insert into car values ("bmw", "750iL")');

        $adapter->query(
            'insert into boat values (1, "boat", "super 8")');

    }

}

