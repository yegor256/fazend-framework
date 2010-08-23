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
        'tar' => 'tar', // shell executable
    );
    
    /**
     * Archive files from the directory into a single file. Also we
     * can add extra directories, which will be added to the TAR archive.
     *
     * @return void
     */
    public function forward() 
    {
        // all files into .TAR
        $file = tempnam(TEMP_PATH, 'fz');
        $cmd = $this->_var('tar') . " -c --file=\"{$file}\" ";

        foreach($this->_getConfig()->content->files->toArray() as $dir) {
            $cmd .= "\"{$dir}/*\"";
        }

        $cmd .= " 2>&1";
        FaZend_Exec::exec($cmd);
    }
    
}
