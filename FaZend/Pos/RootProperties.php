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
     * @return void
     **/
    protected function _setParent(FaZend_Pos_Abstract $parent, $name) 
    {
        FaZend_Exception::raise('FaZend_Pos_RootException',
            "You can't attach POS root to any other object");
    }
    
    /**
     * Get parent
     * 
     * @return FaZend_Pos_Abstract
     */
    protected function _getParent()
    {
        FaZend_Exception::raise('FaZend_Pos_RootException',
            "You can't get parent from ROOT");
    }

}
