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
 * Compress (GZIP) all files in a directory.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Compress_Gzip extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'gzip'   => 'gzip', // shell executable
        'suffix' => 'gz', // suffix for compressed files
    );
    
    /**
     * Compress every file in the directory.
     *
     * @return void
     * @todo FaZend_Backup_Policy_Compress_Gzip_Exception
     * @see FaZend_Backup_Policy_Abstract::forward()
     * @see FaZend_Backup::execute()
     */
    public function forward() 
    {
        $cnt = 0;
        foreach (new DirectoryIterator($this->_dir) as $f) {
            if ($f->isDot()) {
                continue;
            }
            $file = $f->getPathname();
            if (is_dir($file)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Compress_Gzip_Exception',
                    "GZIP can't compress a directory '{$f}', use Archive first"
                );
            }
            $cmd = escapeshellcmd($this->_options['gzip']) 
                . ' --suffix=' . escapeshellarg('.' . $this->_options['suffix'])
                . ' ' . escapeshellarg($file) 
                . ' 2>&1';

            $result = FaZend_Exec::exec($cmd);
            $zip = $file . '.' . $this->_options['suffix'];
            if (!@file_exists($zip)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Compress_Gzip_Exception',
                    "GZIP of '{$f}' failed '{$cmd}': '{$result}'"
                );
            }
            if (!@filesize($zip)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Compress_Gzip_Exception',
                    "GZIP of '{$f}' created empty file '{$cmd}': '{$result}'"
                );
            }
            logg(
                "File '%s' gzipped (into %d bytes, named as %s)",
                pathinfo($file, PATHINFO_BASENAME),
                filesize($zip),
                pathinfo($zip, PATHINFO_BASENAME)
            );
            $cnt++;
        }
        if (!$cnt) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Compress_Gzip_Exception',
                "No files to GZIP, the directory is empty"
            );
        }
    }
    
    /**
     * Compress every file in the directory.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::backward()
     */
    public function backward() 
    {
        /**
         * @todo implement it and UNZIP the file
         */
    }
    
}
