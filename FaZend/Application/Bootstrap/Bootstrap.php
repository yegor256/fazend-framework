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
 * @see Zend_Application_Bootstrap_ResourceBootstrapper
 */
require_once 'Zend/Application/Bootstrap/ResourceBootstrapper.php';

/**
 * @see Zend_Application_Bootstrap_Bootstrapper
 */
require_once 'Zend/Application/Bootstrap/Bootstrapper.php';

/**
 * @see Zend_Application_Bootstrap_BootstrapAbstract
 */
require_once 'Zend/Application/Bootstrap/BootstrapAbstract.php';

/**
 * @see Zend_Application_Bootstrap_Bootstrap
 */
require_once 'Zend/Application/Bootstrap/Bootstrap.php';

/**
 * @see Zend_Application
 */
require_once 'Zend/Application.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';

/**
 * Bootstrap
 *
 * @package Application
 * @subpackage Bootstrap
 */
class FaZend_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    /**
     * Create and return an instance of Zend_Application
     *
     * @return Zend_Application
     * @see index.php
     */
    public static function prepareApplication() 
    {
        $application = new Zend_Application(APPLICATION_ENV);
        Zend_Registry::set('Zend_Application', $application);

        // load application-specific options
        $options = new Zend_Config_Ini(FAZEND_PATH . '/Application/application.ini', 'global', true);
        $options->merge(new Zend_Config_Ini(APPLICATION_PATH . '/config/app.ini', APPLICATION_ENV));

        // include sub-INI files, if necessary
        if (isset($options->resources->fazend->includes)) {
            foreach ($options->resources->fazend->includes as $path) {
                if (!file_exists($path)) {
                    throw new Zend_Exception("File not found: '{$path}'");
                }
                $options->merge(new Zend_Config_Ini($path, APPLICATION_ENV));
            }
        }

        // if the application doesn't have a bootstrap file
        if (!file_exists($options->bootstrap->path)) {
            $options->bootstrap->path = FAZEND_PATH . '/Application/Bootstrap/Bootstrap.php';
            $options->bootstrap->class = 'FaZend_Application_Bootstrap_Bootstrap';
        }                                             

        // load system options
        $application->setOptions($options->toArray());
        return $application;
    }
    
    /**
     * Execute a resource
     *
     * This method protects us in migration from version to version. If 
     * resource is not found, we just ignore this situation.
     *
     * @param string Name of resource
     * @return void
     */
    protected function _executeResource($resource)
    {
        return parent::_executeResource($resource);
        try {
            return parent::_executeResource($resource);
        } catch (Zend_Application_Bootstrap_Exception $e) {
            // swallow it...
            trigger_error(
                "Resource '{$resource}' is deprecated", 
                E_USER_WARNING
            );
        }
    }
    
}

