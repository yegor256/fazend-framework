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
 * Resource for initializing FaZend framework
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_fz_routes extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Initializes the resource
     *
     * @return void
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init() 
    {
        $this->_bootstrap->bootstrap('frontController');
        $front = $this->_bootstrap->getResource('frontController');

        // configure global routes for all
        $router = new Zend_Controller_Router_Rewrite();

        // routes for custom application operations
        $appRoutes = new Zend_Config_Ini(
            FAZEND_PATH . '/Application/app-routes.ini', // absolute path to file
            'global', // section name in INI file
            true
        );

        // specific customizable controllers
        foreach ($appRoutes->routes as $name=>$routeConfig) {
            $controllerFile = APPLICATION_PATH . '/controllers/' . 
                ucfirst($routeConfig->defaults->controller) . 'Controller.php';
            // is it a custom controller?
            if (file_exists($controllerFile)) {
                $routeConfig->defaults->module = 'default';
            }
            $router->addRoute($name, Zend_Controller_Router_Route::getInstance($routeConfig));
        }

        // configure custom routes
        if (file_exists(APPLICATION_PATH . '/config/routes.ini')) {
            $router->addConfig(
                new Zend_Config_Ini(
                    APPLICATION_PATH . '/config/routes.ini', 
                    APPLICATION_ENV
                ), 
                'routes'
            );
        }

        // load standard system routes
        $router->addConfig(
            new Zend_Config_Ini(
                FAZEND_PATH . '/Application/routes.ini', 
                'global'
            ), 
            'routes'
        );

        $front->setRouter($router);
    }    

}
