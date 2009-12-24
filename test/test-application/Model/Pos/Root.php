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
     * Counts how much init method was called
     *
     * @var integer
     **/
    public static $initCounter = 0;
    
    /**
     * Initialize it
     *
     * @return void
     **/
    public function init() 
    {
        parent::init();
        self::$initCounter++;
        
        $cnt = count($this);
        
        // this may potentially lead to endless recursion
        $cnt = count(FaZend_Pos_Abstract::root());

        FaZend_Pos_Abstract::root()->someCar = $car = new Model_Pos_Car();

        $car[] = new Model_Pos_Bike();
        $car[] = new Model_Pos_Bike();
        $car[] = new Model_Pos_Bike();

        $car['the owner'] = 'John Smith';
        $car['the owner1'] = 'John Smith';
        $car['the owner2'] = 'John Smith';
        $car['the owner3'] = 'John Smith';
        $car['the owner4'] = 'John Smith';
        $car->label = 'test';
        $car->object = new FaZend_StdObject();
        FaZend_Pos_Abstract::root()->anotherCar = $car = new Model_Pos_Car();
        $car['the owner'] = 'Michael Jackson';
        
        FaZend_Pos_Abstract::root()->ps()->save();
    }

}


