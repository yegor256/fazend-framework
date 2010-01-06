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
     * Retrieve all owners 
     *
     * @baseline team@fazend.com exists()
     * @return Model_Owner[]
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


