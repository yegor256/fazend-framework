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
 * Initialize test injector
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 * @see FaZend_Test_Injector
 */
class FaZend_Application_Resource_fz_injector extends Zend_Application_Resource_ResourceAbstract
{
    
    /**
     * Injector object (policy)
     *
     * @var FaZend_Test_Injector
     * @see init()
     */
    protected $_injector = null;

    /**
     * Initializes the resource
     *
     * @return FaZend_Test_Injector|null
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init() 
    {
        if (APPLICATION_ENV == 'production') {
            return null;
        }

        if (!is_null($this->_injector)) {
            return $this->_injector;
        }

        // logger comes first
        $this->_bootstrap->bootstrap('fz_logger');

        // start test session
        $this->getBootstrap()->bootstrap('fz_starter');

        // objects in 'test/Mocks' directory
        $mocks = APPLICATION_PATH . '/../../test/Mocks';
        if (file_exists($mocks) && is_dir($mocks)) {
            Zend_Loader_Autoloader::getInstance()->registerNamespace('Mocks_');
        }

        $injectorPhp = APPLICATION_PATH . '/../../test/injector/Injector.php';
        if (!file_exists($injectorPhp)) {
            return $this->_injector = false;
        }

        eval('require_once $injectorPhp;'); // workaround for ZCA validator
        $this->_injector = new Injector();
        $this->_injector->setResource($this);
        $this->_injector->inject();
        return $this->_injector;
    }
    
}
