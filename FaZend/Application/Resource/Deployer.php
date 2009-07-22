<?php
/**
 *
 * Copyright (c) FaZend.com
 * All rights reserved.
 *
 * You can use this product "as is" without any warranties from authors.
 * You can change the product only through Google Code repository
 * at http://code.google.com/p/fazend
 * If you have any questions about privacy, please email privacy@fazend.com
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Resource for initializing FaZend_Deployer
 *
 * @uses       Zend_Application_Resource_Base
 * @category   FaZend
 * @package    FaZend_Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_Deployer extends Zend_Application_Resource_ResourceAbstract {

    /**
    * Defined by Zend_Application_Resource_Resource
    *
    * @return boolean
    */
    public function init() {

        // db is mandatory
        if (!$this->getBootstrap()->hasPluginResource('db'))
            return;
        $this->getBootstrap()->bootstrap('db');

        $options = $this->getOptions();

        // configure deployer and deploy DB schema
        $deployer = new FaZend_Deployer(new Zend_Config($options));
        $deployer->deploy();

        return true;

    }
}
