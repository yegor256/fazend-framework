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
        'host' => '127.0.0.1',
        'port' => '21',
        'username' => 'anonymous',
        'password' => '',
        'dir' => '.',
    );
    
    /**
     * Upload files by FTP.
     *
     * @return void
     */
    public function upload() 
    {
        
    }
    
    /**
     * Send this file by FTP
     *
     * @param string File name
     * @return void
     */
    protected function _sendToFTP($file, $object)
    {
        $host = $this->_getConfig()->ftp->host;
        // if FTP host is not specified in backup.ini - we skip this method
        if (empty($host)) {
            $this->_log("Since [ftp.host] is empty, we won't send files to FTP");
            return;
        }

        $ftp = @ftp_connect($this->_getConfig()->ftp->host);
        if ($ftp === false) {
            $this->_log("Failed to connect to ftp '{$host}'", true);    
        }

        $this->_log("Logged in successfully to '{$host}'");    

        $username = $this->_getConfig()->ftp->username;
        $password = $this->_getConfig()->ftp->password;
        if (@ftp_login($ftp, $username, $password) === false) {
            $this->_log(
                "Failed to login to '{$host}' as '{$username}'", 
                true
            );    
        }

        $this->_log("Connected successfully to FTP as '{$username}'");    

        if (@ftp_pasv($ftp, true) === false) {
            $this->_log("Failed to turn PASV mode ON", true);    
        }

        if (!@ftp_chdir($ftp, $this->_getConfig()->ftp->dir)) {
            $this->_log("Failed to go to '{$this->_getConfig()->ftp->dir}'", true);    
        }

        $this->_log("Current directory in FTP: " . @ftp_pwd($ftp));    

        if (@ftp_put($ftp, $object, $file, FTP_BINARY) === false) {
            $this->_log("Failed to upload " . $this->_nice($file), true);    
        } else {
            $this->_log("Uploaded by FTP: " . $this->_nice($file));    
        }

        if (!@ftp_close($ftp)) {
            $this->_log("Failed to close connection to '{$host}'");    
        }

        $this->_log("Disconnected from FTP");

        // remove expired data files
        $this->_cleanFTP();
    }

    /**
     * Clear expired files from FTP
     *
     * @return void
     */
    protected function _cleanFTP()
    {
        // if FTP file removal is NOT required - we skip this method execution
        if (empty($this->_getConfig()->ftp->age)) {
            $this->_log("Since [ftp.age] is empty we won't remove old files from FTP");
            return;
        }

        // this is the minimum time we would accept
        $minTime = time() - $this->_getConfig()->ftp->age * 24 * 60 * 60;

        $ftp = @ftp_connect($this->_getConfig()->ftp->host);
        if (!$ftp) {
            $this->_log("Failed to connect to ftp ({$this->_getConfig()->ftp->host})");    
            return;
        }

        $this->_log("Logged in successfully to {$this->_getConfig()->ftp->host}");    

        if (!@ftp_login($ftp, $this->_getConfig()->ftp->username, $this->_getConfig()->ftp->password)) {
            $this->_log("Failed to login to ftp ({$this->_getConfig()->ftp->host})");    
            return;
        }

        $this->_log("Connected successfully to FTP as {$this->_getConfig()->ftp->username}");    

        if (!@ftp_pasv($ftp, true)) {
            $this->_log("Failed to turn PASV mode ON");    
            return;
        }

        if (!@ftp_chdir($ftp, $this->_getConfig()->ftp->dir)) {
            $this->_log("Failed to go to {$this->_getConfig()->ftp->dir}");    
            return;
        }

        $this->_log("Current directory in FTP: " . ftp_pwd($ftp));    

        $files = @ftp_nlist($ftp, '.');    
        if (!$files) {
            $this->_log("Failed to get nlist from FTP", true);    
        }

        foreach ($files as $file) {
            $lastModified = @ftp_mdtm($ftp, $file);
            if ($lastModified == -1) {
                $this->_log("Failed to get mdtm from '$file'");    
                continue;
            }    

            if ($lastModified < $minTime) {
                if (!@ftp_delete($ftp, $file)) {
                    $this->_log("Failed to delete file $file");
                } else {
                    $this->_log("File $file removed, since it's expired (over {$this->_getConfig()->S3->age} days)");
                }
            }    
        }

        if (!@ftp_close($ftp)) {
            $this->_log("Failed to close connection to ftp ({$this->_getConfig()->ftp->host})");    
        }

        $this->_log("Disconnected from FTP");    
    }
    
}
