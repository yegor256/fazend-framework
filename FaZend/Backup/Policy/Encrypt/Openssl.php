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
 * @version $Id: Encrypt.php 2113 2010-08-23 13:18:48Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Encrypt content, using OpenSSL algorithms.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Encrypt_Openssl extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'algorithm' => 'blowfish', // tool to use + algorithm
        'password' => 'empty', 
    );
    
    /**
     * Encrypt the files in the directory.
     *
     * @return void
     */
    public function forward() 
    {
        
    }
    
    /**
     * Decrypt the files in the directory.
     *
     * @return void
     */
    public function backward() 
    {
        
    }
    
    /**
     * Encrypt one file and change its name
     *
     * @param string File name
     * @return void
     */
    protected function _encrypt(&$file)
    {
        $fileEnc = $file . '.enc';

        $password = $this->_getConfig()->password;

        $this->_log($this->_nice($file) . " is sent to openssl/blowfish encryption");
        $cmd = $this->_var('openssl') . " enc -blowfish -pass pass:\"{$password}\" < {$file} > {$fileEnc} 2>&1";
        FaZend_Exec::exec($cmd);

        if (file_exists($fileEnc) && (filesize($fileEnc) > 1024)) {
            $this->_log($this->_nice($fileEnc) . " was created");
        } else {
            $this->_log("Command: {$cmd}");
            $this->_log($this->_nice($fileEnc) . " creation error: " . file_get_contents($fileEnc), true);
        }

        $this->_log($this->_nice($file) . " deleted");
        unlink($file);

        $this->_log($this->_nice($fileEnc) . " renamed");
        rename($fileEnc, $file);
    }
    
}
