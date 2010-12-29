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
 * @version $Id: Database.php 2113 2010-08-23 13:18:48Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * @see FaZend_Backup_Policy_Abstract
 */
require_once 'FaZend/Backup/Policy/Abstract.php';

/**
 * Dump content of the MySQL database.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Dump_Mysql extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'mysqldump' => 'mysqldump', // shell executive absolute/relative path
        'dbname'    => null,
        'username'  => null,
        'password'  => null,
        'file'      => '{name}.mysqldump',
    );

    /**
     * Dump DB content to disc.
     *
     * @return void
     * @throws FaZend_Backup_Policy_Dump_Mysql_Exception
     * @see FaZend_Backup_Policy_Abstract::forward()
     * @see FaZend_Backup::execute()
     */
    public function forward()
    {
        $file = $this->_dir . '/' . $this->_options['file'];
        if (file_exists($file)) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Dump_Mysql_Exception',
                "File '{$file}' already exists, pick up another name"
            );
        }

        // get connection params from default adapter
        if (is_null($this->_options['dbname'])) {
            $db = Zend_Db_Table::getDefaultAdapter();
            if (is_null($db)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Dump_Mysql_Exception',
                    "Default Zend_Db adapter is not configured, specify options explicitly instead"
                );
            }
            $config = $db->getConfig();
            $this->_options['dbname'] = $config['dbname'];
            $this->_options['username'] = $config['username'];
            $this->_options['password'] = $config['password'];
        }

        $cmd = escapeshellcmd($this->_options['mysqldump'])
            . ' -v -u '
            . escapeshellarg($this->_options['username'])
            . ' --force --password='
            . escapeshellarg($this->_options['password'])
            . ' ' . escapeshellarg($this->_options['dbname'])
            . ' --result-file='
            . escapeshellarg($file)
            . ' 2>&1';
        $result = FaZend_Exec::exec($cmd);

        if (!file_exists($file)) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Dump_Mysql_Exception',
                "MySQL is not dumped: '{$result}'"
            );
        }
        if (filesize($file) < 1024) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Dump_Mysql_Exception',
                "MySQL dump is too small: " . file_get_contents($file)
            );
        }
        logg(
            'MySQL dump created: %s (%d bytes)',
            pathinfo($file, PATHINFO_BASENAME),
            filesize($file)
        );
    }

    /**
     * Restore DB from dump image.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::backward()
     */
    public function backward()
    {
        /**
         * @todo implement it to restore the MySQL db from the
         * dumped content file.
         */
    }

}
