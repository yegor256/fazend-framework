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
 * Abstract backup policy.
 *
 * @package Backup
 */
abstract class FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array();
    
    /**
     * Set options before execution.
     *
     * @param array List of options, associative array
     * @return void
     */
    public final function setOptions(array $options)
    {
        foreach ($options as $k=>$v) {
            if (!array_key_exists($k, $this->_options)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Abstract_Exception',
                    "Invalid option '{$k}' for " . get_class($this)
                );
            }
            $this->_options[$k] = $v;
        }
    }
    
    /**
     * Show nice filename
     *
     * @param string Absolute file name
     * @return string
     */
    protected function _nice($file)
    {
        if (!file_exists($file)) {
            return basename($file) . ' (absent)';
        }
        return basename($file) . ' (' . filesize($file). 'bytes)';
    }

}
