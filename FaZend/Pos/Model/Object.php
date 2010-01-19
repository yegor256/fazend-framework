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
 * 'fzObject' PHP representative
 * 
 * @package Pos
 */
class FaZend_Pos_Model_Object extends FaZend_Db_Table_ActiveRow_fzObject
{

    /**
     * Create a new DB object for the given POS class
     * 
     * @param FaZend_Pos_Abstract Child object
     * @param FaZend_Pos_Abstract Parent object
     * @param string Name inside the parent
     * @return FaZend_Pos_Model_Object
     */
    public static function create(FaZend_Pos_Abstract $pos, FaZend_Pos_Abstract $parent, $name)
    {
        $object = new self();
        $object->class = get_class($pos);
        $object->save();
        
        FaZend_Pos_Model_PartOf::create($object, $parent->ps()->fzObject, $name);
        
        return $object;
    }

    /**
     * Retrieive's a Model object by parent and name in it
     * 
     * @param FaZend_Pos_Abstract Parent object
     * @param string Name inside the parent
     * @return FaZend_Pos_Model_Object
     * @throws FaZend_Pos_Model_Object_NotFoundException
     */
    public static function findByParent(FaZend_Pos_Abstract $parent, $name)
    {
        return self::retrieve()
            ->join('fzPartOf', 'fzObject.id = fzPartOf.kid', array())
            ->where('fzPartOf.name = ?', $name)
            ->where('fzPartOf.parent = ?', strval($parent->ps()->id))
            ->setRowClass('FaZend_Pos_Model_Object')
            ->fetchRow()
            ;
    }

    /**
     * Retrieve root object
     * 
     * @return FaZend_Pos_Model_Object
     */
    public static function findRoot()
    {
        try {
            return self::retrieve()
                ->where('id = 1')
                ->setRowClass('FaZend_Pos_Model_Object')
                ->fetchRow()
                ;
        } catch (FaZend_Pos_Model_Object_NotFoundException $e) {
            $root = new FaZend_Pos_Model_Object();
            $root->class = 'FaZend_Pos_Root';
            $root->save();
            return $root;
        }
    }

}
