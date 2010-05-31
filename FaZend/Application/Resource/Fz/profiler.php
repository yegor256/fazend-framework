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
 * DB profiler in test environment
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_fz_profiler extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Initializes the resource
     *
     * @return void
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init() 
    {
        // profiler is used ONLY in development environment
        if (APPLICATION_ENV === 'production') {
            return;
        }

        // disable it during CLI unit testing
        if (defined('TESTING_RUNNING')) {
            return;
        }

        // maybe there is no DB in the application, sometimes it happens :)
        if (!$this->_bootstrap->hasPluginResource('db')) {
            return;
        }

        // turn ON the profiler
        $this->_bootstrap->bootstrap('db');
        $this->_bootstrap->getResource('db')->setProfiler(true);
    }

}
