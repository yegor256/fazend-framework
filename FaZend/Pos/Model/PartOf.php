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
 * TODO: short description.
 * 
 * TODO: long description.
 * 
 */
class FaZend_Pos_Model_PartOf extends FaZend_Db_Table_ActiveRow_fzPartOf
{

    /**
     * Create a new DB object
     * 
     * @param FaZend_Pos_Model_Object Child object
     * @param FaZend_Pos_Model_Object Parent object
     * @param string Name inside the parent
     * @return FaZend_Pos_Model_PartOf
     */
    public static function create(FaZend_Pos_Model_Object $kid, FaZend_Pos_Model_Object $parent, $name)
    {
        $partOf = new FaZend_Pos_Model_PartOf();
        $partOf->name = $name;
        $partOf->kid = $kid;
        $partOf->parent = $parent;
        $partOf->save();
        
        return $partOf;
    }

    /**
     * Find by parent and name
     * 
     * @param FaZend_Pos_Model_Object Parent object
     * @param string Name inside the parent
     * @return FaZend_Pos_Model_PartOf
     */
    public static function findByParent(FaZend_Pos_Model_Object $parent, $name)
    {
        return self::retrieve()
            ->where('parent = ?', $parent)
            ->where('name = ?', $name)
            ->setRowClass('FaZend_Pos_Model_PartOf')
            ->limit(1)
            ->fetchRow();
    }

    /**
     * Find all kids
     * 
     * @param FaZend_Pos_Model_Object Parent object
     * @return FaZend_Pos_Model_PartOf[]
     */
    public static function retrieveByParent(FaZend_Pos_Model_Object $parent)
    {
        return self::retrieve()
            ->where('parent = ?', $parent)
            ->setRowClass('FaZend_Pos_Model_PartOf')
            ->fetchAll();
    }

}
