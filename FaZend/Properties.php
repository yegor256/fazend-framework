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
 * Properties of FaZend
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 * @package Properties
 */
class FaZend_Properties {

    /**
     * All properties
     *
     * @var Zend_Config
     */
    private static $_options = array();

    /**
     * Save options
     *
     * @return void
     */
    public static function setOptions(Zend_Config $config) {
        self::$_options = $config;
    }

    /**
     * Returns a config
     *
     * @return Zend_Config
     */
    public static function get() {
        return self::$_options;
    }

}
