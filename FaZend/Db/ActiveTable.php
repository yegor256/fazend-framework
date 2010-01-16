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
     * Returns table class properly configured
     *
     * @param string Name of the table in the DB
     * @return void
     * @throws FaZend_Db_Wrapper_NoIdFieldException
     */
    public static function createTableClass($table)
    {
        $tableClassName = 'FaZend_Db_ActiveTable_' . $table;

        $cls = new $tableClassName();

        // this page has primary key and Zend automatically detects it?
        try {
            $cls->info(Zend_Db_Table_Abstract::PRIMARY);
            // possible exceptions:
            // - Zend_Db_Table_Exception
            // - Zend_Db_Adapter_Mysqli_Exception
        } catch (Exception $e) {
            // no, we can't detect it automatically
            $cls = new $tableClassName(array(
                'primary' => 'id'
            ));

            try {
                // maybe we just have a field ID?
                $cls->info(Zend_Db_Table_Abstract::PRIMARY);
            } catch (Exception $e2) {
                FaZend_Exception::raise(
                    'FaZend_Db_Wrapper_NoIdFieldException',
                    "Table {$table} doesn't have either a primary or ID field. " .
                    " Error1: " . $e->getMessage() . 
                    '. Error2: ' . $e2->getMessage()
                );
            }    
        }

        return $cls;    
    }
    
}
