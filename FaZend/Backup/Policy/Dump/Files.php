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
 * Dump files in FS.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Dump_Files extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'prefix' => null,
        'dirs' => array(),
    );
    
    /**
     * Backup files
     *
     * @return void
     */
    protected function _backupFiles()
    {
        // if files backup is NOT specified in backup.ini - we skip it
        if (empty($this->_getConfig()->content->files)) {
            $this->_log("Since [content.file] is empty, we won't backup files");
            return;
        }

        // all files into .TAR
        $file = tempnam(TEMP_PATH, 'fz');
        $cmd = $this->_var('tar') . " -c --file=\"{$file}\" ";

        foreach($this->_getConfig()->content->files->toArray() as $dir) {
            $cmd .= "\"{$dir}/*\"";
        }

        $cmd .= " 2>&1";
        FaZend_Exec::exec($cmd);

        // encrypt the .TAR
        $this->_encrypt($file);

        // archive it into .GZ
        $this->_archive($file);

        // unique name of the backup file
        $object = $this->_getConfig()->archive->files->prefix . date('ymd-his') . '.data';

        // send to FTP
        $this->_sendToFTP($file, $object);

        // send to amazon
        $this->_sendToS3($file, $object);
    }

}
