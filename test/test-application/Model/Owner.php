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
     */
    function isMe()
    {
        return true;
    }

    /**
     * Get full details of the owner
     *
     * @baseline team@fazend.com exists()
     * @return Model_Owner_Details
     */
    function getDetails()
    {
        $details = new Model_Owner_Details();

        return $details
            ->set('name', $this->name)
            ->set('id', $this->__id)
            ->set('balance', rand(100, 999));
    }

}


