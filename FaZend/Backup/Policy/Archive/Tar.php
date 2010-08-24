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
 * Archiver of multiple files into one, using TAR.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Archive_Tar extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'tar'  => 'tar', // shell executable
        'file' => '{name}-archive.tar', // file name of TAR archive
        'dirs' => array(), // extra directories to add
    );
    
    /**
     * Archive files from the directory into a single file. Also we
     * can add extra directories, which will be added to the TAR archive.
     *
     * @return void
     * @throws FaZend_Backup_Policy_Archive_Tar_Exception
     */
    public function forward() 
    {
        $file = $this->_dir . '/' . $this->_options['file'];
        if (file_exists($file)) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Archive_Tar_Exception',
                "File '{$file}' already exists, pick up another name"
            );
        }
        
        $cmd = escapeshellcmd($this->_options['tar']) 
            . ' -c --file='
            . escapeshellarg($file)
            . ' ' . escapeshellarg($this->_dir);

        foreach($this->_options['dirs'] as $dir) {
            if (!file_exists($dir)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Archive_Tar_Exception',
                    "File/directory is absent: '{$dir}'"
                );
            }
            $cmd .= ' ' . escapeshellarg($dir);
        }

        $cmd .= ' 2>&1';
        
        $result = FaZend_Exec::exec($cmd);

        if (!file_exists($file)) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Archive_Tar_Exception',
                "TAR into '{$file}' failed: '{$result}'"
            );
        }
        logg(
            'TAR archive created at %s (%d bytes)',
            pathinfo($file, PATHINFO_BASENAME),
            filesize($file)
        );
    }
    
    /**
     * Restore files from the file into a directory.
     *
     * @return void
     */
    public function backward() 
    {
        /**
         * @todo implement it
         */
    }
    
}
