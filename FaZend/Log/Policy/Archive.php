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
 * @version $Id: Log.php 1747 2010-03-17 19:17:38Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Archive the file every day/week/month...
 *
 * @package Log
 * @see logg()
 */
class FaZend_Log_Policy_Archive extends FaZend_Log_Policy_Abstract
{

    /**
     * List of available options
     *
     * @var array
     */
    protected $_options = array(
        'lifetime' => 7, // duration in hours to keep the log, in days
        'maxAge'   => 30, // how long to keep a file in archive, days
    );

    /**
     * Run the policy
     *
     * @return void
     * @throws FaZend_Log_Policy_Archive_Exception
     */
    protected function _run()
    {
        if ($this->_options['lifetime'] < 1) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Archive_Exception',
                "Lifetime shall be greater than 1 day: '{$this->_options['lifetime']}'"
            );
        }

        if ($this->_options['lifetime'] >= $this->_options['maxAge']) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Archive_Exception',
                "Lifetime '{$this->_options['lifetime']}' shall be bigger than maxAge '{$this->_options['maxAge']}'"
            );
        }

        // the file is still too young?
        if (@filectime($this->_file) > time() - $this->_options['lifetime'] * 24*60*60) {
            return;
        }
        
        $archive = dirname($this->_file) . '/' . pathinfo($this->_file, PATHINFO_FILENAME) 
            . date('-Ymd');
            
        // maybe this place is occupied already?
        if (file_exists($archive)) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Archive_Exception',
                "Archive destination '{$archive}' already exists"
            );
        }
        
        // make a copy
        copy($this->_file, $archive);
        
        // kill the log
        $this->_truncate($this->_file);

        // protocol this operation
        logg(
            'previous log was archived (%dKb) to %s',
            filesize($archive),
            $archive
        );
        
        // kill old files
        foreach (glob(dirname($this->_file) . '/' . pathinfo($this->_file, PATHINFO_FILENAME) . '-*') as $f) {
            $path = dirname($this->_file) . '/' . $f;
            if (filemtime($path) < time() - $this->_options['maxAge'] * 24*60*60) {
                if (false === @unlink($path)) {
                    FaZend_Exception::raise(
                        'FaZend_Log_Policy_Archive_Exception',
                        "Failed to delete old archive: '{$path}'"
                    );
                }
                // protocol this operation
                logg(
                    'archive log deleted (%dKb) at %s, since it is too old',
                    filesize($path),
                    $path
                );
            }
        }
    }

}
