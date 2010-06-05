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
 * @see Zend_Db_Table
 */
require_once 'Zend/Db/Table.php';

/**
 * Simple table
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 * @package Db
 */
abstract class FaZend_Db_ActiveTable extends Zend_Db_Table
{
    
    /**
     * Cached array of classes
     *
     * @var FaZend_Db_ActiveTable
     */
    protected static $_cached = array();

    /**
     * Clean static cache, used for unit testing
     *
     * If we don't clean this cache after every unit test we might
     * see a problem when DB adapter is no longer active, but our static
     * table instances are attached to it.
     *
     * @return void
     */
    public static function cleanCache() 
    {
        self::$_cached = array();
    }

    /**
     * Returns table class properly configured
     *
     * @param string Name of the table in the DB
     * @return FaZend_Db_ActiveTable
     * @throws FaZend_Db_Wrapper_NoIdFieldException
     */
    public static function createTableClass($table)
    {
        $tableClassName = 'FaZend_Db_ActiveTable_' . $table;
        if (array_key_exists($table, self::$_cached)) {
            return self::$_cached[$table];
        }

        /**
         * Create new table class and store it in a local static cache
         */
        $cls = self::$_cached[$table] = new $tableClassName();

        /**
         * This table has primary key and Zend automatically detects it?
         */
        try {
            $cls->info(Zend_Db_Table_Abstract::PRIMARY);
            /**
             * Possible exceptions here:
             * - Zend_Db_Table_Exception
             * - Zend_Db_Adapter_Mysqli_Exception
             */
        } catch (Exception $e) {
            try {
                /**
                 * No, we can't detect it automatically, let's assume
                 * that primary key is "ID". Maybe we just have a field "ID", 
                 * which is not a primary key, but is named properly?
                 */
                $cls = self::$_cached[$table] = new $tableClassName(array('primary' => 'id'));
                $cls->info(Zend_Db_Table_Abstract::PRIMARY);
            } catch (Exception $e2) {
                FaZend_Exception::raise(
                    'FaZend_Db_Wrapper_NoIdFieldException',
                    "Table {$table} doesn't have either a primary key and it doesn't have field 'ID'. "
                    . $e->getMessage() . '. '
                    . $e2->getMessage()
                );
            }    
        }
        return $cls;    
    }
    
}
