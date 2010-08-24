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
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * @see FaZend_Backup
 */
require_once 'FaZend/Backup.php';

/**
 * Backup of DB and files.
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_fz_backup extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Initializes the resource..
     *
     * @return FaZend_Backup
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init() 
    {
        // configure deployer and deploy DB schema
        $backup = FaZend_Backup::getInstance();
        $backup->setOptions($this->getOptions());
        return $backup;
    }
}
