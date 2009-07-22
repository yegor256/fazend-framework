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
 * Deploy Db schema
 *
 * @package FaZend 
 */
class FaZend_Deployer {

    /**
     * Configuration options
     *
     * @var Zend_Config
     */
    protected $_options;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(Zend_Config $config) {
        $this->_config = $config;
    }

    /**
     * Deploy Db schema
     *
     * @return void
     */
    public function deploy() {

        // check the existence of the flag
        // it it's absent, we should do anything
        $flagFile = APPLICATION_PATH . '/deploy/flag.txt';
        if (!file_exists($flagFile) && (APPLICATION_ENV !== 'testing'))
            return;

        // remove it
        // we will never come back here again
        if ((@unlink($flagFile) === false) && (APPLICATION_ENV !== 'testing')) {
            $this->_log('Failed to remove flag file: ' . $flagFile);
            return;
        }

        $dir = APPLICATION_PATH . '/deploy/database';
        if (!file_exists($dir) || !is_dir($dir)) {
            return;
        }

        // if we can't get a list of tables in DB - we stop
        if (!method_exists($this->_db(), 'listTables'))
            return;
        
        // get full list of existing(!) tables in Db
        $tables = $this->_db()->listTables();
        
        // get full list of files
        $files = preg_grep('/^\d+\s.*?\.sql$/', scandir($dir));

        // sort in right order
        usort($files, array($this, '_sorter'));

        // go through all .SQL files
        foreach ($files as $file) {

            $matches = array();
            preg_match('/^\d+\s(.*)\.sql$/', $file, $matches);
            $table = $matches[1];

            // this table already exists?
            if (in_array($table, $tables)) {
                $this->_update($table, file_get_contents($dir . '/' . $file));
            } else {
                $this->_create($table, file_get_contents($dir . '/' . $file));
            }

        }

    }

    /**
     * Create new table
     *
     * @param string Name of the table
     * @param string SQL file content
     * @return void
     */
    protected function _create($table, $sql) {
        
        $this->_db()->query($sql);

    }

    /**
     * Update existing table
     *
     * @param string Name of the table
     * @param string SQL file content
     * @return void
     */
    protected function _update($table, $sql) {

        $infoDb = $this->_db()->describeTable($table);

        $infoSql = $this->_sqlInfo($sql);

        foreach ($infoSql as $column);
    
    }

    /**
     * Get list of columns from SQL spec
     *
     * @param string SQL spec of the table
     * @return array[]
     */
    protected function _sqlInfo($sql) {

        // TBD
        return array();
        
    }

    /**
     * Internal logger
     *
     * @param string Message to log
     * @return void
     */
    protected function _log($msg) {
        
    }

    /**
     * File name sorter
     *
     * @param string File name 1
     * @param string File name 2
     * @return int
     */
    protected function _sorter($file1, $file2) {
        
        return (int)$file1 > (int)$file2;

    }

    /**
     * Db adapter
     *
     * @return Zend_Db_Adapter
     */
    protected function _db() {
        return Zend_Db_Table::getDefaultAdapter();
    }

}
