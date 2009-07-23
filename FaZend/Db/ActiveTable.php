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

require_once 'Zend/Db/Table.php';

/**
 * Simple table
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 */
abstract class FaZend_Db_ActiveTable extends Zend_Db_Table {

    /**
     * Returns table class properly configured
     *
     * @param string Name of the table in the DB
     * @return void
     */
    public static function createTableClass($table) {

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
            $cls = new $tableClassName(array('primary'=>'id'));

            try {
                // maybe we just have a field ID?
                $cls->info(Zend_Db_Table_Abstract::PRIMARY);
            
            } catch (Exception $e2) {

                FaZend_Exception::raise('FaZend_Db_Wrapper_NoIDFieldException',
                    "Table {$table} doesn't have either a primary or ID field. Error1: " . $e->getMessage() . '. Error2: ' . $e2->getMessage());
            }    
        }

        return $cls;    

    }

    
}
