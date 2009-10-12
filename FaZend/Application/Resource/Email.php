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
 * Resource for initializing FaZend_Email
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_Email extends Zend_Application_Resource_ResourceAbstract {

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return boolean
     */
    public function init() {

        // get options from INI file
        $options = $this->getOptions();

        // make sure view is initialized
        $this->_bootstrap->bootstrap('view');

        // save configuration into static class
        FaZend_Email::config(new Zend_Config($options), $this->_bootstrap->getResource('view'));

        return true;
        
    }
}
