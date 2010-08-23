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
 * Dump content of the database.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Dump_Database extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'tool'     => 'mysqldump',
        'dbname'   => null,
        'username' => null,
        'password' => null,
        'prefix'   => null,
    );
    
    /**
     * Backup db
     *
     * @return void
     */
    protected function _backupDatabase()
    {
        // if we should not backup DB - we skip it
        if (empty($this->_getConfig()->content->db)) {
            $this->_log("Since [content.db] is empty, we won't backup database");
            return;
        }

        // mysqldump
        $file = tempnam(TEMP_PATH, 'fz');
        $config = Zend_Db_Table::getDefaultAdapter()->getConfig();

        // @see: http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html
        $cmd = $this->_var('mysqldump').
            " -v -u \"{$config['username']}\" --force ".
            "--password=\"{$config['password']}\" \"{$config['dbname']}\" --result-file=\"{$file}\" 2>&1";
        
        $result = FaZend_Exec::exec($cmd);

        if (file_exists($file) && (filesize($file) > 1024)) {
            $this->_log($this->_nice($file) . " was created with SQL database dump: $cmd");
        } else {
            $this->_log("Command: {$cmd}");
            $this->_log($this->_nice($file) . " creation error: " . $result, true);
        }

        // encrypt the SQL
        $this->_encrypt($file);

        // archive it into .GZ
        $this->_archive($file);

        // unique name of the backup file
        $object = $this->_getConfig()->archive->db->prefix . date('ymd-his') . '.data';

        // send to FTP
        $this->_sendToFTP($file, $object);

        // send to amazon
        $this->_sendToS3($file, $object);

        // kill the file
        unlink($file);
    }
    
}
