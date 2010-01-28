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
 * Properties of FaZend
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 * @package Properties
 */
class FaZend_Properties
{

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
    public static function setOptions(Zend_Config $config)
    {
        self::$_options = $config;
    }

    /**
     * Returns a config
     *
     * @return Zend_Config
     */
    public static function get()
    {
        return self::$_options;
    }

}
