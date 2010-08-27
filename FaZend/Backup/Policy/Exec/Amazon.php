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
 * Execute some shell commands on Amazon EC2 instance.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Exec_Amazon extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
    );
    
    /**
     * Execute commands in EC2 instance.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::forward()
     * @see FaZend_Backup::execute()
     */
    public function forward() 
    {
        // create a new EC2 instance
        // start it
        // execute commands remotely
        // stop the instance
        // delete the instance
    }
    
    /**
     * Restore files from Amazon S3 bucket into directory.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::backward()
     */
    public function backward() 
    {
        
    }
    
}
