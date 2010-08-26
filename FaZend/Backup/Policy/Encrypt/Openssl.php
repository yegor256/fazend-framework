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
 * @see FaZend_Backup_Policy_Abstract
 */
require_once 'FaZend/Backup/Policy/Abstract.php';

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
        'openssl'   => 'openssl', // executable cmd
        'algorithm' => 'blowfish', // algorithm to use
        'password'  => 'pass:empty', // could be a file name or a password with 'pass:' prefix
        'suffix'    => 'enc', // suffix to add to the file
    );
    
    /**
     * Encrypt the files in the directory.
     *
     * @return void
     * @throws FaZend_Backup_Policy_Encrypt_Openssl_Exception
     * @see FaZend_Backup_Policy_Abstract::forward()
     * @see FaZend_Backup::execute()
     */
    public function forward() 
    {
        foreach (new DirectoryIterator($this->_dir) as $f) {
            if ($f->isDot()) {
                continue;
            }
            $file = $f->getPathname();
            if (is_dir($file)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Encrypt_Openssl_Exception',
                    "OpenSSL can't encrypt a directory '{$file}', use Archive first"
                );
            }
            $temp = tempnam(TEMP_PATH, __CLASS__);
            $cmd = escapeshellcmd($this->_options['openssl']) 
                . ' enc -'
                . $this->_options['algorithm']
                . ' -pass '
                . escapeshellarg($this->_options['password']) 
                . ' < '
                . escapeshellarg($file) 
                . ' > '
                . escapeshellarg($temp)
                . ' 2>&1';

            $result = FaZend_Exec::exec($cmd);
            if (!@filesize($temp)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Encrypt_Openssl_Exception',
                    "Failed to encrypt file: '{$result}'"
                );
            }
            if (@unlink($file) === false) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Encrypt_Openssl_Exception',
                    "Failed to unlink('{$file}')"
                );
            }
            $enc = $file . '.' . $this->_options['suffix'];
            if (file_exists($enc)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Encrypt_Openssl_Exception',
                    "File '{$enc}' already exists, can't rename '{$file}'"
                );
            }
            if (@rename($temp, $enc) === false) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Encrypt_Openssl_Exception',
                    "Failed to rename('{$temp}', '{$enc}')"
                );
            }

            logg(
                "File '%s' encrypted (into %d bytes, named as %s)",
                pathinfo($file, PATHINFO_BASENAME),
                filesize($enc),
                pathinfo($enc, PATHINFO_BASENAME)
            );
        }
    }
    
    /**
     * Decrypt the files in the directory.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::backward()
     */
    public function backward() 
    {
        /**
         * @todo implement it and decrypt the file
         */
    }
    
}
