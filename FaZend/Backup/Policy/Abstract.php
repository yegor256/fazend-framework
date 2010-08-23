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
     * Directory where we're working.
     *
     * @var string
     */
    protected $_dir = null;
    
    /**
     * Forward execution of the policy.
     *
     * @return void
     */
    abstract public function forward();
    
    /**
     * BACKWARD execution of the policy.
     *
     * @return void
     */
    abstract public function backward();
    
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
     * Set directory to work in.
     *
     * @param string Absolute path of the directory
     * @return void
     */
    public function setDir($dir) 
    {
        if (!@file_exists($dir) || !@is_dir($dir)) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Abstract_Exception',
                "Directory is absent: '{$dir}'"
            );
        }
        $this->_dir = $dir;
    }

}
