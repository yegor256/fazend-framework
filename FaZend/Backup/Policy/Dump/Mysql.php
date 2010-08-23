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
        'dbname'   => null,
        'username' => null,
        'password' => null,
    );
    
    /**
     * Dump DB content to disc.
     *
     * @return void
     */
    public function dump() 
    {
        $file = $this->_dir . '/' . get_class($this) . '.dump';
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
                "MySQL is not dumped with '{$cmd}': '{$result}'"
            );
        }
        if (filesize($file) < 1024) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Dump_Mysql_Exception',
                "MySQL dump is too small after '{$cmd}': " . file_get_contents($file)
            );
        }
        logg(
            'MySQL dump created: %s (%s bytes)',
            $file,
            filesize($file)
        );
    }
    
}
