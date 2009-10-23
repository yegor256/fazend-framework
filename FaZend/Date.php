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
 * Zend_Date decorator
 *
 * @package Date
 */
class FaZend_Date {

    /**
     * Create Zend_Date object on-fly
     *
     * @return FaZend_Date
     **/
    public static function make($time) {
        return new FaZend_Date($time);
    }

}
