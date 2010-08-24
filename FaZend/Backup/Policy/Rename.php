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
 * @version $Id: Archive.php 2113 2010-08-23 13:18:48Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * @see FaZend_Backup_Policy_Abstract
 */
require_once 'FaZend/Backup/Policy/Abstract.php';

/**
 * Rename files in the directory, according to the pattern.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Rename extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'suffix'  => '-y-M-dd-HH-mm', // suffix to append to every file, according to Zend_Date
    );
    
    /**
     * Rename every file according to the suffix provided.
     *
     * @return void
     * @throws FaZend_Backup_Policy_Rename_Exception
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
                    'FaZend_Backup_Policy_Rename_Exception',
                    "We can't rename directory '{$f}', use Archive first"
                );
            }

            $dest = $file . Zend_Date::now()->get($this->_options['suffix']);
            if (file_exists($dest)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Rename_Exception',
                    "File '{$dest}' already exists, can't rename '{$file}'"
                );
            }
            if (@rename($file, $dest) === false) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Rename_Exception',
                    "Failed to rename('{$file}', '{$dest}')"
                );
            }
            logg(
                "File '%s' renamed to '%s'",
                pathinfo($file, PATHINFO_BASENAME),
                pathinfo($dest, PATHINFO_BASENAME)
            );
        }
        
    }
    
    /**
     * Restore file names.
     *
     * @return void
     */
    public function backward() 
    {
        /**
         * @todo implement it and rename files back
         */
    }
    
}
