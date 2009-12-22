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
        $object = new FaZend_Pos_Model_Object();
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
