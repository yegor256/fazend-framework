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
 * @baseline team@fazend.com exists()
 */

/**
 * ORM auto-mapping classes
 * 
 *
 * @package application
 * @subpackage Model
 * @baseline team@fazend.com exists()
 * @baseline team@fazend.com instanceOf('FaZend_Db_Table_ActiveRow_owner')
 */
class Model_Owner extends FaZend_Db_Table_ActiveRow_owner
{
    
    /**
     * Create new owner
     *
     * @return Model_Owner
     */
    public static function create($name) 
    {
        $owner = new self();
        $owner->name = $name;
        $owner->save();
        return $owner;
    }

    /**
     * Retrieve all owners 
     *
     * @baseline team@fazend.com exists()
     * @return Model_Owner[]
     * @see FaZend_Db_Table_ActiveRow
     */
    public static function retrieveAll()
    {
        return self::retrieve()
            ->setRowClass('Model_Owner')
            ->fetchAll();
    }

    /**
     * Is it me?
     *
     * @baseline team@fazend.com exists()
     * @return boolean
     * @see FaZend_Cli_cli_BaselineTest
     */
    public function isMe()
    {
        return true;
    }

    /**
     * Get full details of the owner
     *
     * @baseline team@fazend.com exists()
     * @return Model_Owner_Details
     */
    public function getDetails()
    {
        $details = new Model_Owner_Details();
        return $details
            ->set('name', $this->name)
            ->set('id', $this->__id)
            ->set('balance', rand(100, 999));
    }
    
    /**
     * Register new person
     *
     * This method is used for forma validation.
     *
     * @return void
     * @see views/scripts/index/forma.phtml
     * @see FaZend_View_Helper_FormaTest
     */
    public static function register($name, $reason, $client, $address, $file = null) 
    {
        validate()
            ->type($client, 'boolean', "Invalid type of CLIENT")
            ->type($address, 'string', "Invalid type of ADDRESS")
            ->type($name, 'string', "Invalid type of NAME")
            ->type($name, 'string', "Invalid type of REASON")
            ;
        
        logg('Successfull Registration of new Owner: ++success++');
    }

}


