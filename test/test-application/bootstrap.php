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
 * @see ActorSystem
 */

/**
 * Bootstraper
 *
 * This file could be omitted
 *
 * @package application
 * @see ActorSystem
 */
class Bootstrap extends FaZend_Application_Bootstrap_Bootstrap
{

    /**
     * Initialize DB schema
     *
     * @return void
     * @see Model_Owner
     */
    protected function _initDbData()
    {
        $this->bootstrap('fz_injector');
        $this->bootstrap('fz_deployer');
        $this->bootstrap('fz_orm');
        FaZend_Db_Table_ActiveRow::addMapping('/owner\.created/', 'new Zend_Date(${a1})');

        // explicitly deploy DB
        $deployer = new FaZend_Db_Deployer();
        $deployer->setFolders(array(APPLICATION_PATH . '/deploy/database'));
        $deployer->setVerbose(true);
        $deployer->deploy();
        // bug(555);
        
        $queries = array(
            'insert into owner values (132, "john smith", null)',
            'insert into product values (10, "car", 132)',
            'insert into car values ("bmw", "750iL")',
            'insert into boat values (1, "boat", "super 8")',
        );

        foreach ($queries as $query) {
            Zend_Db_Table_Abstract::getDefaultAdapter()->query($query);
        }
    }
    
    /**
     * Initialize forma() helper
     *
     * @return void
     * @see index/forma.phtml
     */
    protected function _initForma() 
    {
        FaZend_View_Helper_Forma_Field::addPluginDir(
            'Helper_Forma_FieldDate',
            realpath(APPLICATION_PATH . '/helpers/Forma')
        );
    }

    /**
     * Initialize FaZend_User class
     *
     * @return void
     * @see Model_User
     */
    public function _initUserClass() 
    {
        $this->bootstrap('fz_Injector');
        $this->bootstrap('fz_orm');
        FaZend_User::setRowClass('Model_User');
    }

}

