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
 * Dynamic properties for ROOT
 * 
 * @package Pos
 */
class FaZend_Pos_RootProperties extends FaZend_Pos_Properties
{
    
    /**
     * Saves all changes to the DB
     *
     * @return void
     **/
    public function saveAll() 
    {
        foreach (self::$_instances as $property)
            $property->save(false);
    }
    
    /**
     * Find object by ID
     *
     * @param integer ID of the object (fzObject.id)
     * @return FaZend_Pos_Abstract
     * @throws FaZend_Pos_Root_ObjectNotFound
     **/
    public function findById($id) 
    {
        $fzObject = new FaZend_Pos_Model_Object(intval($id));
        if (!$fzObject->exists())
            FaZend_Exception::raise('FaZend_Pos_Root_ObjectNotFound',
                "Object can't be found by id:{$id}",
                'FaZend_Pos_Exception');
            
        $className = $fzObject->class;
        if (is_subclass_of($className, 'FaZend_Pos_Root') || ($className === 'FaZend_Pos_Root')) {
            return FaZend_Pos_Abstract::root();
        } else {
            $obj = new $className();
            $obj->ps()->recoverById($id);
            return $obj;
        }
    }

    /**
     * Validate whether the object is already in POS
     *
     * @return void
     **/
    protected function _attachToPos() 
    {
        $this->_parent = false;
        $this->_fzObject = FaZend_Pos_Model_Object::findRoot();
        return parent::_attachToPos();
    }

    /**
     * Set parent for the object
     * 
     * @param FaZend_Pos_Abstract The object, which is parent
     * @param string Unique name inside the parent
     * @return nothing
     * @throws FaZend_Pos_RootException Always
     **/
    protected function _setParent(FaZend_Pos_Abstract $parent, $name) 
    {
        FaZend_Exception::raise('FaZend_Pos_RootException',
            "You can't attach POS root to any other object");
    }
    
    /**
     * Get parent
     * 
     * @return nothing
     * @throws FaZend_Pos_RootException Always
     */
    protected function _getParent()
    {
        FaZend_Exception::raise('FaZend_Pos_RootException',
            "You can't get parent from ROOT");
    }

    /**
     * Get path
     * 
     * @return Path of root
     */
    protected function _getPath()
    {
        return 'root';
    }

}
