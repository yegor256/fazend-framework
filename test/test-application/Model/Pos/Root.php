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
 * POS simple ROOT object
 *
 * @package application
 * @subpackage Model
 */
class Model_Pos_Root extends FaZend_Pos_Root
{
    
    /**
     * Initialize it
     *
     * @return void
     **/
    public function init() 
    {
        parent::init();
        
        $cnt = count($this);
        
        // this may potentially lead to endless recursion
        $cnt = count(FaZend_Pos_Abstract::root());
        $car = $this->car;
    }

}


