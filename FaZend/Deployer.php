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

    const EXCEPTION_CLASS = 'FaZend_Deployer_Exception';

    /**
     * Instance of the class
     *
     * @var FaZend_Deployer
     */
    protected static $_instance;

    /**
     * Configuration options
     *
     * @var Zend_Config
     */
    protected $_options;

    /**
     * Instance of DB deployer
     *
     * @param Zend_Config Configuration parameters
     * @return void
     */
    public static function getInstance(Zend_Config $config = null) {
        if (!isset(self::$_instance)) {
            if (is_null($config))
                FaZend_Exception::raise('FaZend_Deployer_InvalidConfig', 
                    "First time getInstance() should be called with valid config",
                    self::EXCEPTION_CLASS);

            self::$_instance = new FaZend_Deployer($config);
        }

        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @param Zend_Config Configuration parameters
     * @return void
     */
    protected function __construct(Zend_Config $options) {
        $this->_options = $options;
    }

    /**
     * Deploy Db schema
     *
     * @return void
     */
    public function deploy() {

        // if it's turned off
        if (!$this->_options->deploy)
            return;
        
        // check the existence of the flag
        // it it's absent, we should do anything
        $flagFile = $this->_flagName();
        if (!file_exists($flagFile) && (APPLICATION_ENV === 'production'))
            return;

        // remove it
        // we will never come back here again
        if ((@unlink($flagFile) === false) && (APPLICATION_ENV === 'production')) {
            $this->_log('Failed to remove flag file: ' . $flagFile);
            return;
        }

        $dir = $this->_dirName();
        if (!file_exists($dir) || !is_dir($dir)) {
            return;
        }

        // if we can't get a list of tables in DB - we stop
        if (!method_exists($this->_db(), 'listTables'))
            return;
        
        try {

            // get full list of existing(!) tables in Db
            $tables = array_map(create_function('$a', 'return strtolower($a);'), $this->_db()->listTables());
            
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
                if (in_array(strtolower($table), $tables)) {
                    $this->_update($table, file_get_contents($dir . '/' . $file));
                } else {
                    $this->_create($table, file_get_contents($dir . '/' . $file));
                }

            }

        } catch (FaZend_Deployer_Exception $exception) {

            // if there is no email - show the error
            if (FaZend_Properties::get()->errors->email) {

                // send email to the site admin admin
                FaZend_Email::create('fazendDeployerException.tmpl')
                    ->set('toEmail', FaZend_Properties::get()->errors->email)
                    ->set('toName', 'Admin of ' . WEBSITE_URL)
                    ->set('subject', parse_url(WEBSITE_URL, PHP_URL_HOST) . ' database deployment exception, rev.' . FaZend_Revision::get())
                    ->set('text', $exception->getMessage())
                    ->send()
                    ->logError();

             }

             // throw it to the application
             throw $exception;

        } 

    }

    /**
     * Get list of tables ready for deployment
     *
     * @return string[]
     */
    public function getTables() {

        $list = array();

        $matches = array();
        foreach (scandir($this->_dirName()) as $file) {
            if (!preg_match('/^\d+\s(.*?)\.sql$/', $file, $matches))
                continue;

            try {
                $this->getTableInfo($matches[1]);
            } catch (FaZend_Deployer_NotTableButView $e) {
                continue;
            }

            $list[] = $matches[1];
        }

        return $list;

    }

    /**
     * Get table information
     *
     * @param string Name of the table
     * @return array[]
     */
    public function getTableInfo($table) {

        $dir = $this->_dirName();
        foreach (scandir($dir) as $file)
            if (preg_match('/^\d+\s' . preg_quote($table) . '\.sql$/i', $file))
                return $this->_sqlInfo(file_get_contents($dir . '/' . $file));

        FaZend_Exception::raise('FaZend_Deployer_SqlFileNotFound', "File '<num> {$table}.sql' not found in '{$dir}'", self::EXCEPTION_CLASS);

    }

    /**
     * Get table information from sql
     *
     * @param string SQL
     * @return array[]
     */
    public function getSqlInfo($sql) {
        return $this->_sqlInfo($sql);
    }

    /**
     * Location of .SQL files, directory
     *
     * @return string
     */
    protected function _dirName() {
        return $this->_options->folder;
    }

    /**
     * Location of the flag
     *
     * @return string
     */
    protected function _flagName() {
        return $this->_options->flag;
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

        try {

            $infoSql = $this->_sqlInfo($sql);

        } catch (FaZend_Deployer_NotTableButView $e) {

            // this is VIEW, not table
            // we just drop and create again
            $this->_db()->query("DROP VIEW $table");

            // create this VIEW again
            $this->_create($table, $sql);

            return;
        }

        $infoDb = $this->_db()->describeTable($table);

        // tbd
        foreach ($infoSql as $column);
    
    }

    /**
     * Get list of columns from SQL spec
     *
     * @param string SQL spec of the table
     * @return array[]
     */
    protected function _sqlInfo($sql) {

        $sql = preg_replace(array(
            '/--.*?\n/', // kill comments
            '/[\n\t\r]/', // no special chars
            '/\s+/', // compress spaces
            '/`/', // remove backticks
            ), ' ', $sql . "\n");

        // no double spaces
        $sql = trim($sql);

        // sanity check
        if (!preg_match('/^create (?:table|view)?/i', $sql))
            FaZend_Exception::raise('FaZend_Deployer_WrongFormat', 
                "Every SQL file should start with 'create table' or 'create view', we get this: " . cutLongLine($sql, 50),
                self::EXCEPTION_CLASS);

        // this is view, we just drop it and create new
        if (preg_match('/^create view/i', $sql))
            FaZend_Exception::raise('FaZend_Deployer_NotTableButView');

        // cut out the text between starting and ending brackets
        $columnsText = substr($sql, strpos($sql, '(')+1);
        $columnsText = trim(substr($columnsText, 0, strrpos($columnsText, ')'))) . ', ';

        $matches = array();
        preg_match_all('/([\w\d\_]+)\s+((?:[\w\_\s\d]|(?:\(.*?\)))+)(?:\scomment\s[\"\'](.*?)[\'\"])?\,/i', $columnsText, $matches);

        $info = array();
        foreach ($matches[0] as $id=>$column) {

            // skip primary key
            if (preg_match('/^(primary\skey|index|constraint|unique|foreign\skey)\s?\(/i', $column))
                continue;

            $info[$matches[1][$id]] = array(
                'COLUMN_NAME' => $matches[1][$id],
                'DATA_TYPE' => $matches[2][$id],
                'COMMENT' => $matches[3][$id],
            );
        }

        return $info;
        
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
