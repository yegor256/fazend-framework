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

require_once 'Zend/Application/Bootstrap/Bootstrap.php';

/**
* Bootstrap
*
* @package FaZend_Application
*/
class FaZend_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    /**
     * Initialize front controller options
     *
     * @return void
     */
    protected function _initFrontControllerOptions() {

        // make sure the front controller already bootstraped
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');

        // throw exceptions if failed
        // only in development/testing environment
        // or in CLI execution
        if ((APPLICATION_ENV !== 'production') || defined('CLI_ENVIRONMENT'))
            $front->throwExceptions(true);

        // setup error plugin
        $front->registerPlugin(new Zend_Controller_Plugin_ErrorHandler(array(
            'module' => 'fazend',
            'controller' => 'error',
            'action' => 'error'
        )));

    }
        
    /**
     * Initialize key options of Zend_View
     *
     * @return void
     */
    protected function _initViewOptions() {

        // make sure the view already bootstraped
        $this->bootstrap('view');
        $view = $this->getResource('view');

        // save View into registry
        Zend_Registry::getInstance()->view = $view;

        // set the type of docs
        $view->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);

        // set proper paths for view helpers and filters
        $view->addHelperPath(APPLICATION_PATH . '/helpers', 'Helper');
        $view->addHelperPath(FAZEND_PATH . '/View/Helper', 'FaZend_View_Helper');
        $view->addFilterPath(FAZEND_PATH . '/View/Filter', 'FaZend_View_Filter');

        // turn ON html compressor, if necessary
        $this->bootstrap('Fazend');
        if (FaZend_Properties::get()->htmlCompression)
            $view->addFilter('HtmlCompressor');

        // view paginator
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginationControl.phtml');

        // session
        if (defined('CLI_ENVIRONMENT'))
            Zend_Session::$_unitTestEnabled = true;

    }

    /**
     * Configure FaZend if the application is NOT in Zend framework
     *
     * @return void
     */
    protected function _initBlindFaZend() {

        // make sure it is loaded already
        $this->bootstrap('layout');

        // layout reconfigure, if necessary
        $layout = Zend_Layout::getMvcInstance();
        if (!file_exists($layout->getViewScriptPath()))
            $layout->setViewScriptPath(FAZEND_PATH . '/View/layouts/scripts');

        // controller
        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');
        $dirs = $front->getControllerDirectory();

        if (!file_exists($dirs['default']))
            $front->setControllerDirectory(FAZEND_PATH . 'Controller/controllers/default', 'default');

    }        

    /**
     * Configure routes
     *
     * @return void
     */
    protected function _initRoutes() {

        $this->bootstrap('frontController');
        $front = $this->getResource('frontController');

        // configure global routes for all
        $router = new Zend_Controller_Router_Rewrite();

        // load standard routes, later customer will change them (two lines below)
        $router->addConfig(new Zend_Config_Ini(FAZEND_PATH . '/Application/routes.ini', 'global'), 'routes');

        $appRoutes = new Zend_Config_Ini(FAZEND_PATH . '/Application/app-routes.ini', 'global', true);

        // specific customizable controllers
        foreach ($appRoutes->routes as $name=>$routeConfig) {

            // is it a custom controller?
            if (file_exists(APPLICATION_PATH . '/controllers/' . ucfirst($routeConfig->defaults->controller) . 'Controller.php')) {
                $routeConfig->defaults->module = 'default';
            }

            $router->addRoute($name, Zend_Controller_Router_Route::getInstance($routeConfig));
        }

        // configure custom routes
        if (file_exists(APPLICATION_PATH . '/config/routes.ini')) {
            $router->addConfig(new Zend_Config_Ini(APPLICATION_PATH . '/config/routes.ini', APPLICATION_ENV), 'routes');
        }

        $front->setRouter($router);

    }    

    /**
     * Configure profiler for development environment
     *
     * @return void
     */
    protected function _initDbProfiler() {

        // profiler is used ONLY in development environment
        if ((APPLICATION_ENV !== 'development'))
            return;

        // maybe there is no DB in the application, sometimes it happens :)
        if (!$this->hasPluginResource('db'))
            return;

        $this->bootstrap('db');
        $db = $this->getResource('db');

        // turn ON the profiler
        $db->setProfiler(true);

    }

}

