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
 * Deploy Db schema to the server (MySQL supported for now only)
 *
 * You may add your own pathes with .SQL files by means of app.ini in your project.
 * See application.ini for resources.Deployer.folders parameter.
 *
 * @package Deployer
 * @todo Refactor in order to support other DB servers
 * @see FaZend_Application_Resource_fz_deployer
 */
class FaZend_Db_Deployer
{
    
    /**
     * Database adapter to use
     *
     * @var Zend_Db_Adapter_Abstract
     * @see setAdapter()
     */
    protected $_adapter = null;

    /**
     * Absolute name of the file with flag
     *
     * @var string
     * @see deploy()
     */
    protected $_flag;
    
    /**
     * List of absolute directory names with .SQL files
     *
     * @var string[]
     * @see deploy()
     */
    protected $_folders = array();
    
    /**
     * Shall this class be LOG-verbose (add messages to log)
     *
     * @var string
     * @see _create()
     * @see _update()
     */
    protected $_verbose = true;

    /**
     * Set flag (absolute file name of the flag)
     *
     * @param string Absolute file name
     * @return $this
     * @see FaZend_Application_Resource_fz_deployer::init()
     */
    public function setFlag($flag) 
    {
        $this->_flag = $flag;
        return $this;
    }

    /**
     * Set list of directories with .SQL files
     *
     * @param string[] List of directories
     * @return $this
     * @throws FaZend_Db_Deployer_InvalidFolderException
     * @see FaZend_Application_Resource_fz_deployer::init()
     */
    public function setFolders(array $folders) 
    {
        foreach ($folders as $dir) {
            if (!file_exists($dir) || !is_dir($dir)) {
                FaZend_Exception::raise(
                    'FaZend_Db_Deployer_InvalidFolderException', 
                    "Directory '{$dir}' is absent or is not a directory"
                );
            }
        }
        $this->_folders = $folders;
        return $this;
    }

    /**
     * Set verbose option
     *
     * @param boolean Shall this class use LOG for create/update events notification?
     * @return $this
     * @see FaZend_Application_Resource_fz_deployer::init()
     */
    public function setVerbose($verbose) 
    {
        $this->_verbose = $verbose;
        return $this;
    }
    
    /**
     * Set adapter to use
     *
     * @param Zend_Db_Adapter_Abstract Adapter to use
     * @return $this
     */
    public function setAdapter(Zend_Db_Adapter_Abstract $adapter) 
    {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * Deploy Db schema
     *
     * @return void
     */
    public function deploy() 
    {
        // shall we deploy?
        if (!$this->_isNecessary()) {
            return;
        }
        
        // go through ALL deployment directories
        foreach ($this->_folders as $dir) {
            try {
                // get full list of existing(!) tables in Db
                $tables = array_map(
                    create_function('$a', 'return strtolower($a);'), 
                    $this->_adapter->listTables()
                );
            
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
                        $this->_update($table, $this->_clearSql($dir . '/' . $file));
                    } else {
                        $this->_create($table, $this->_clearSql($dir . '/' . $file));
                    }
                }
            } catch (FaZend_Db_Deployer_Exception $e) {
                // swallow it and report in log
                FaZend_Log::err("Deployment exception: {$e->getMessage()}");
            } 
        }
    }

    /**
     * Get list of tables ready for deployment
     *
     * @return string[]
     */
    public function getTables() 
    {
        $list = $matches = array();
        foreach ($this->_folders as $dir) {
            foreach (scandir($dir) as $file) {
                if (!preg_match('/^\d+\s(.*?)\.sql$/', $file, $matches)) {
                    continue;
                }

                try {
                    $this->getTableInfo($matches[1]);
                } catch (FaZend_Db_Deployer_NotTableButView $e) {
                    assert($e instanceof Exception); // for ZCA only
                    continue;
                }

                $list[] = $matches[1];
            }
        }
        return $list;
    }

    /**
     * Get table information
     *
     * @param string Name of the table
     * @return array[]
     * @throws FaZend_Db_Deployer_SqlFileNotFound
     */
    public function getTableInfo($table) 
    {
        foreach ($this->_folders as $dir) {
            foreach (scandir($dir) as $file) {
                if (preg_match('/^\d+\s' . preg_quote($table) . '\.sql$/i', $file)) {
                    return $this->_sqlInfo(file_get_contents($dir . '/' . $file));
                }
            }
        }
        FaZend_Exception::raise(
            'FaZend_Db_Deployer_SqlFileNotFound', 
            "File '<num> {$table}.sql' not found in '{$dir}'",
            'FaZend_Db_Deployer_Exception'
        );
        return null; // for ZCA only
    }

    /**
     * Get table information from sql
     *
     * @param string SQL
     * @return array[]
     */
    public function getSqlInfo($sql) 
    {
        return $this->_sqlInfo($sql);
    }

    /**
     * Deployment is required right now?
     *
     * @see deploy()
     * @return boolean
     */
    protected function _isNecessary() 
    {
        // check the existence of the flag
        // it it's absent, we should do NOT anything
        if ((APPLICATION_ENV === 'production') && !file_exists($this->_flag)) {
            return false;
        }

        // remove it
        // we will never come back here again
        if ((APPLICATION_ENV === 'production') && (@unlink($this->_flag) === false)) {
            FaZend_Log::err("Failed to remove deployer flag file: '{$this->_flag}'");
            return false;
        }

        // if we can't get a list of tables in DB - we stop
        if (!method_exists($this->_adapter, 'listTables')) {
            return false;
        }
        return true;
    }

    /**
     * Create new table
     *
     * @param string Name of the table
     * @param string SQL file content
     * @return void
     * @throws FaZend_Db_Deployer_Exception
     */
    protected function _create($table, $sql) 
    {
        if (empty($sql) && $this->_verbose) {
            logg("DB table '{$table}' was NOT created since SQL is empty");
            return;
        }
        
        try {
            $this->_adapter->query($sql);
        } catch (Exception $e) {
            FaZend_Exception::raise(
                'FaZend_Db_Deployer_CreateFailed', 
                $e->getMessage() . ': ' . $sql, 
                'FaZend_Db_Deployer_Exception'
            );
        }
        
        // log the operation
        if ($this->_verbose) {
            logg("DB table '{$table}' was created: {$sql}");
        }
    }

    /**
     * Update existing table
     *
     * @param string Name of the table
     * @param string SQL file content
     * @return void
     * @todo this method is not implemented yet
     */
    protected function _update($table, $sql) 
    {
        assert(is_string($table)); // for ZCA only
        assert(is_string($sql)); // for ZCA only
        // try {
            // $infoSql = $this->_sqlInfo($sql);
        // } catch (FaZend_Db_Deployer_NotTableButView $e) {
            // this is VIEW, not table
            // we just drop and create again
            //$this->_adapter->query("DROP VIEW $table");

            // create this VIEW again
            //$this->_create($table, $sql);
        //     return;
        // }

        // $infoDb = $this->_adapter->describeTable($table);

        // tbd
        // foreach ($infoSql as $column);

        // log the operation
        // FaZend_Log::info("DB table '{$table}' was updated: {$sql}");
    }

    /**
     * Get list of columns from SQL spec
     *
     * @param string SQL spec of the table
     * @return array[]
     */
    protected function _sqlInfo($sql) 
    {
        $sql = preg_replace(
            array(
                '/\-\-.*?\n/', // kill comments
                '/[\n\t\r]/', // no special chars
                '/\s+/', // compress spaces
                '/`/', // remove backticks
            ), 
            ' ', 
            $sql . "\n"
        );

        // no double spaces
        $sql = trim($sql);

        // sanity check
        if (!preg_match('/^create (?:table|view)?/i', $sql))
            FaZend_Exception::raise(
                'FaZend_Db_Deployer_WrongFormat', 
                "Every SQL file should start with 'create table' or 'create view', ".
                "we get this: '" . cutLongLine($sql, 50) . "'",
                'FaZend_Db_Deployer_Exception'
            );

        // this is view, we just drop it and create new
        if (preg_match('/^create\s(?:or\sreplace\s)?view/i', $sql)) {
            FaZend_Exception::raise(
                'FaZend_Db_Deployer_NotTableButView'
            );
        }

        // cut out the text between starting and ending brackets
        $columnsText = substr($sql, strpos($sql, '(')+1);
        $columnsText = trim(substr($columnsText, 0, strrpos($columnsText, ')'))) . ', ';

        $matches = array();
        preg_match_all(
            '/([\w\d\_]+)\s+((?:[\w\_\s\d]|(?:\(.*?\)))+)(?:\scomment\s[\"\'](.*?)[\'\"])?\,/i', 
            $columnsText, 
            $matches
        );

        $info = array();
        foreach ($matches[0] as $id=>$column) {
            $key = array();
            // primary key
            if (preg_match('/^primary\skey\s?.*?\(\s?([^\,]*?)\s?\)/i', $column, $key)) {
                $info[$key[1]]['PRIMARY'] = 1;
                $info[$key[1]]['PRIMARY_POSITION'] = 1;
                $info[$key[1]]['IDENTITY'] = 1;
                continue;
            }

            // process foreign keys
            if (preg_match(
                '/^(?:constraint\s.*?\s)?foreign\skey\s\(\s?(.*?)\s?\)\sreferences\s(.*?)\s\(\s?(.*?)\s?\)\s(.*)/i', 
                $column, 
                $key
            )) {
                $info[$key[1]]['FK'] = 1;
                $info[$key[1]]['FK_TABLE'] = trim($key[2]);
                $info[$key[1]]['FK_COLUMN'] = trim($key[3]);

                if (strpos(strtolower($key[4]), 'on delete cascade') !== false) {
                    $info[$key[1]]['FK_COMPOSITION'] = true;
                }
                continue;
            }

            // unique's
            if (preg_match('/^(?:unique|unique key)\s?\(\s?([^\,]*?)\s?\)/i', $column, $key)) {
                $info[$key[1]]['UNIQUE'] = 1;
                continue;
            }

            // other special mnemos, if not parsed above - skip them
            if (preg_match('/^(index|key|primary\skey|constraint|foreign\skey|unique|unique\skey)\s?\(/i', $column)) {
                continue;
            }

            $info[$matches[1][$id]] = array(
                'COLUMN_NAME' => $matches[1][$id],
                'DATA_TYPE' => $matches[2][$id],
                'COMMENT' => $matches[3][$id],
                'NULL' => !preg_match('/not\snull/i', $matches[2][$id]),
            );
                
        }
        return $info;
    }

    /**
     * File name sorter
     *
     * @param string File name 1
     * @param string File name 2
     * @return int
     * @see deploy()
     */
    protected function _sorter($file1, $file2) 
    {
        return (int)$file1 > (int)$file2;
    }

    /**
     * Clears SQL out of comments
     *
     * @param string Name of the SQL file
     * @return string
     * @see deploy()
     */
    protected function _clearSql($file) 
    {
        return trim(
            preg_replace(
                array(
                    '/\-\-.*/',
                    '/[\n\r\t]/'
                ), 
                ' ', // replace with spaces
                "\n" . file_get_contents($file)
            )
        );
    }

}
