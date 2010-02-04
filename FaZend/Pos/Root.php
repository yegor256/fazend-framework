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
 * Root for all POS objects
 * 
 * @package Pos
 */
class FaZend_Pos_Root extends FaZend_Pos_Abstract
{

    /**
     * Root exists?
     *
     * @return boolean
     **/
    public static function exists() 
    {
        try {
            FaZend_Pos_Model_Object::findRoot();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Init it
     * 
     * @return void
     */
    public function init()
    {
        parent::init();
        
        FaZend_Db_Table_ActiveRow::addMapping(
            '/^fzSnapshot\.fzObject|fzPartOf\.(?:parent|kid)$/', 
            'FaZend_Pos_Model_Object'
        );
        
        $this->ps(
            FaZend_Pos_RootProperties::factory(
                'FaZend_Pos_RootProperties', 
                $this,
                FaZend_Pos_Model_Object::findRoot()
            )
        );
    }

}
