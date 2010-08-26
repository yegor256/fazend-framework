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
 * @see FaZend_Backup_Policy_Abstract
 */
require_once 'FaZend/Backup/Policy/Abstract.php';

/**
 * Save files to FTP server.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Save_Ftp extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'host'     => 'backup.fazend.com', // FTP host name
        'port'     => '21', // FTP port, 21 by default
        'username' => 'backup', // FTP user name
        'password' => 'open', // FTP password
        'dir'      => './{name}', // directory in the FTP server
        'age'      => 168, // in hours, 7 days by default
    );
    
    /**
     * Save files into FTP.
     *
     * @return void
     * @throws FaZend_Backup_Policy_Save_Ftp_Exception
     * @see FaZend_Backup_Policy_Abstract::forward()
     * @see FaZend_Backup::execute()
     */
    public function forward() 
    {
        $ftp = @ftp_connect($this->_options['host']);
        if ($ftp === false) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Save_Ftp_Exception',
                "ftp_connect('{$this->_options['host']}') failed"
            );
        }

        if (@ftp_login($ftp, $this->_options['username'], $this->_options['password']) === false) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Save_Ftp_Exception',
                "ftp_login('{$this->_options['username']}', '"
                . str_repeat('*', strlen($this->_options['password'])) . "') failed"
            );
        }

        if (@ftp_pasv($ftp, true) === false) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Save_Ftp_Exception',
                'ftp_pasv() failed'
            );
        }

        if (@ftp_chdir($ftp, $this->_options['dir']) === false) {
            if (@ftp_mkdir($ftp, $this->_options['dir']) === false) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Save_Ftp_Exception',
                    "Both ftp_chdir() and ftp_mkdir() failed for '{$this->_options['dir']}'"
                );
            }
        }

        // remove expired files
        $this->_clean($ftp);
        
        foreach (new DirectoryIterator($this->_dir) as $f) {
            if ($f->isDot()) {
                continue;
            }
            $file = $f->getPathname();
            if (is_dir($file)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Save_Ftp_Exception',
                    "Can't upload directory '{$f}' by FTP, use Archive first"
                );
            }

            $dest = pathinfo($file, PATHINFO_BASENAME);
            if (@ftp_put($ftp, $dest, $file, FTP_BINARY) === false) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Save_Ftp_Exception',
                    "ftp_put('{$dest}') failed"
                );
            }
            logg(
                "ftp_put('%s') success, %d bytes",
                $dest,
                filesize($file)
            );
        }
        
        if (@ftp_close($ftp) === false) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Save_Ftp_Exception',
                "ftp_close() failed"
            );
        }
    }
    
    /**
     * Restore files from FTP into directory.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::backward()
     */
    public function backward() 
    {
        
    }
    
    /**
     * Clear expired files from FTP.
     *
     * @param integer FTP connection handler
     * @return void
     * @throws FaZend_Backup_Policy_Save_Ftp_Exception
     */
    protected function _clean($ftp)
    {
        $files = @ftp_nlist($ftp, '.');    
        if ($files === false) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Save_Ftp_Exception',
                "ftp_nlist('.') failed"
            );
        }

        foreach ($files as $file) {
            $lastModified = @ftp_mdtm($ftp, $file);
            if ($lastModified === -1) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Save_Ftp_Exception',
                    "ftp_mdtm('{$file}') failed"
                );
            }    
            $expired = Zend_Date::now()->sub($this->_options['age'], Zend_Date::HOUR)
                ->isLater($lastModified);
            if (!$expired) {
                continue;
            }
            if (@ftp_delete($ftp, $file) === false) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Save_Ftp_Exception',
                    "ftp_delete('{$file}') failed"
                );
            }
            logg(
                "File '%s' removed since it's expired (over %d hours)",
                $file,
                $this->_options['age']
            );
        }
    }
    
}
