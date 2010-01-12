<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

define('LICENSE_FILE', APPLICATION_PATH . '/../../LICENSE.txt');

/**
 * Bootstraper
 *
 * This file could be omitted
 *
 * @package application
 */
class Bootstrap extends FaZend_Application_Bootstrap_Bootstrap
{ 

    public function _initDbData()
    {
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

