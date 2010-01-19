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

/**
* 'fzPartOf' PHP representative
 * 
 * @package Pos
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
        $partOf = new self();
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
     * @throws FaZend_Pos_Model_PartOf_NotFoundException
     */
    public static function findByParent(FaZend_Pos_Model_Object $parent, $name)
    {
        return self::retrieve()
            ->where('parent = ?', strval($parent))
            ->where('name = ?', $name)
            ->setRowClass('FaZend_Pos_Model_PartOf')
            ->limit(1)
            ->fetchRow();
    }

    /**
     * Find name by parent and kid
     * 
     * @param FaZend_Pos_Model_Object Parent object
     * @param FaZend_Pos_Model_Object Kid object
     * @return FaZend_Pos_Model_PartOf
     * @throws FaZend_Pos_Model_PartOf_NotFoundException
     */
    public static function findByParentAndKid(FaZend_Pos_Model_Object $parent, FaZend_Pos_Model_Object $kid)
    {
        return self::retrieve()
            ->where('parent = ?', strval($parent))
            ->where('kid = ?', strval($kid))
            ->setRowClass('FaZend_Pos_Model_PartOf')
            ->limit(1)
            ->fetchRow();
    }

    /**
     * Find first available parent, by the kid ID
     * 
     * @param FaZend_Pos_Model_Object Kid object
     * @return FaZend_Pos_Model_PartOf
     * @throws FaZend_Pos_Model_PartOf_NotFoundException
     */
    public static function findByKid(FaZend_Pos_Model_Object $kid)
    {
        return self::retrieve()
            ->where('kid = ?', strval($kid))
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
            ->where('parent = ?', strval($parent))
            ->setRowClass('FaZend_Pos_Model_PartOf')
            ->fetchAll();
    }

}
