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
 * @version $Id: Abstract.php 1747 2010-03-17 19:17:38Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * GZIP all files in a directory.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Gzip extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'exec' => null, // shell executable mask, to be defined in application.ini
    );
    
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
