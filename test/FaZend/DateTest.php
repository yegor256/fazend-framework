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

require_once 'AbstractTestCase.php';

/**
 * Test case
 *
 * @package tests
 */
class FaZend_DateTest extends AbstractTestCase {
    
    public function testBasicFunctionsWork () {

        $date = FaZend_Date::make(time());
        $start = clone $date;
        $start->sub(1, Zend_Date::MONTH);
        
        $this->assertTrue($date->isBetween($start, $date));

    }

}
        