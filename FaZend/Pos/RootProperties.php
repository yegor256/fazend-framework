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
 * Dynamic properties for ROOT
 * 
 * @package Pos
 */
class FaZend_Pos_RootProperties extends FaZend_Pos_Properties
{
    
    /**
     * Find object by ID
     *
     * @param integer ID of the object (fzObject.id)
     * @return FaZend_Pos_Abstract
     * @throws FaZend_Pos_Root_ObjectNotFound
     */
    public function findById($id) 
    {
        $fzObject = new FaZend_Pos_Model_Object(intval($id));
        if (!$fzObject->exists()) {
            FaZend_Exception::raise(
                'FaZend_Pos_Root_ObjectNotFound',
                "Object can't be found by id:{$id}",
                'FaZend_Pos_Exception'
            );
        }
            
        $className = $fzObject->class;
        if (is_subclass_of($className, 'FaZend_Pos_Root') || ($className === 'FaZend_Pos_Root')) {
            return FaZend_Pos_Properties::root();
        } else {
            $obj = new $className();
            $obj->ps()->recoverById($id);
            return $obj;
        }
    }

    /**
     * Validate whether the object is already in POS
     *
     * @return boolean
     */
    protected function _isInPos() 
    {
        return true;
    }

    /**
     * Set parent for the object
     * 
     * @param FaZend_Pos_Abstract The object, which is parent
     * @param string Unique name inside the parent
     * @return nothing
     * @throws FaZend_Pos_RootException Always
     */
    protected function _setParent(FaZend_Pos_Abstract $parent, $name) 
    {
        FaZend_Exception::raise(
            'FaZend_Pos_RootException',
            "You can't attach POS root to any other object"
        );
    }
    
    /**
     * Get parent
     * 
     * @return nothing
     * @throws FaZend_Pos_RootException Always
     */
    protected function _getParent()
    {
        FaZend_Exception::raise(
            'FaZend_Pos_RootException',
            "You can't get parent from ROOT"
        );
    }

    /**
     * Get my name
     * 
     * @return string
     */
    protected function _getName()
    {
        return 'root';
    }
    
    /**
     * Get array of objects above root
     * 
     * @return array()
     */
    protected function _getUplinks(array $uplinks = array())
    {
        return array();
    }

}
