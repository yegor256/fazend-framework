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
        'gzip' => 'gzip', // shell executable
    );
    
    /**
     * Compress every file in the directory.
     *
     * @return void
     */
    public function forward() 
    {
        
    }
    
    /**
     * Compress every file in the directory.
     *
     * @return void
     */
    public function backward() 
    {
        
    }
    
    /**
     * Archive one file and change its name to .GZ
     *
     * @param string File name
     * @return void
     */
    protected function _archive(&$file)
    {
        $cmd = $this->_var('gzip') . ' ' . escapeshellcmd($file) . ' 2>&1';
        $this->_log($this->_nice($file) . ' is sent to gzip: ' . $cmd);

        $result = FaZend_Exec::exec($cmd);
        $file = $file . '.gz';
        
        if (file_exists($file) && filesize($file)) {
            $this->_log($this->_nice($file) . ' was created');
        } else {
            $this->_log(
                $this->_nice($file) . ' creation error: ' . $result, 
                true
            );
        }
    }

}
