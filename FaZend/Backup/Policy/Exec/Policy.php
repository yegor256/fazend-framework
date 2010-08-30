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
 * @version $Id: Amazon.php 2127 2010-08-27 07:22:09Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * @see FaZend_Backup_Policy_Abstract
 */
require_once 'FaZend/Backup/Policy/Abstract.php';

/**
 * Execute another custom policy.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Exec_Policy extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
        'class' => '?', // PHP class name
        'options' => array(), // options to send to the policy 
    );
    
    /**
     * Execute another policy.
     *
     * @return void
     * @throws FaZend_Backup_Policy_Exec_Amazon_Exception
     * @see FaZend_Backup_Policy_Abstract::forward()
     * @see FaZend_Backup::execute()
     */
    public function forward() 
    {
        $class = $this->_options['class'];
        $policy = new $class();
        $policy->setOptions($this->_options['options']);
        $policy->setDir($this->_dir);
        $policy->forward();
    }
    
    /**
     * Execute the policy, in backward direction.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::backward()
     */
    public function backward() 
    {
        $class = $this->_options['class'];
        $policy = new $class();
        $policy->setOptions($this->_options['options']);
        $policy->setDir($this->_dir);
        $policy->backward();
    }
    
}
