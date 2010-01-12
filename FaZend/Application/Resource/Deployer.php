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

require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Resource for initializing FaZend_Deployer
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_Deployer extends Zend_Application_Resource_ResourceAbstract
{

    /**
    * Defined by Zend_Application_Resource_Resource
    *
    * @return boolean
    */
    public function init() 
    {
        // db is mandatory
        if (!$this->getBootstrap()->hasPluginResource('db'))
            return;
        $this->_bootstrap->bootstrap('db');

        $options = $this->getOptions();

        // configure deployer and deploy DB schema
        FaZend_Deployer::getInstance(new Zend_Config($options))->deploy();

        return true;
    }
}
