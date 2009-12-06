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
 * Resource for initializing FaZend framework
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_Fazend extends Zend_Application_Resource_ResourceAbstract {

    /**
     * List of booted items already
     *
     * @var string[]
     */
    protected $_booted = array();

    /**
     * Initializes the resource (the entire FaZend Framework)
     * 
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Config Configuration of fazend, from INI file
     */
    public function init() {
        $options = $this->getOptions();

        validate()->true(isset($options['name']),
                "[FaZend.name] should be defined in your app.ini file");

        $config = new Zend_Config($options);
        FaZend_Properties::setOptions($config);

        $rc = new ReflectionClass($this);
        foreach ($rc->getMethods() as $method) {
            if (preg_match('/^\_init(\w+)$/', $method->getName(), $matches)) {
                $this->_boot($matches[1]);
            }
        }

        return $config;
    }

    /**
     * Call this method, if it wasn't called yet
     *
     * @return void
     **/
    protected function _boot($name) {
        if (isset($this->_booted[$name]))
            return;
        $method = '_init' . $name;
        
        if (!method_exists($this, $method))
            FaZend_Exception('FaZend_Application_Resource_Fazend_InvalidMethod', 
                "Can't find '{$method}'");
                
        $this->$method();
        $this->_booted[$name] = true;
    }

    /**
     * Configure session properly
     *
     * @return void
     **/
    protected function _initSessionOptions() {
        // if there is NO session - ignore
        if (!$this->_bootstrap->hasPluginResource('session'))
            return;
            
        // if in testing mode - ignore this
        if (Zend_Session::$_unitTestEnabled)
            return;

        // if in testing mode - ignore this
        if (defined('CLI_ENVIRONMENT'))
            return;

        $dir = TEMP_PATH . '/' . FaZend_Properties::get()->name . '-sessions';
        
        // create this directory if necessary
        if (!file_exists($dir)) {
            if (@mkdir($dir) === false)
                trigger_error("Session directory '{$dir}' can't be created", E_USER_WARNING);
        }
            
        // is it available for writing?
        if (file_exists($dir) && is_dir($dir) && is_writable($dir)) {
            $options = array('save_path' => $dir);
            Zend_Session::setOptions($options);
        } else
            trigger_error("Session directory '{$dir}' can't be used", E_USER_WARNING);
        
        // make session alive by COOKIE information
        Zend_Session::rememberMe();
    }

    /**
     * Initialize front controller options
     *
     * @return void
     */
    protected function _initFrontControllerOptions() {
        // make sure the front controller already bootstraped
        $this->_bootstrap->bootstrap('frontController');
        $front = $this->_bootstrap->getResource('frontController');

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
        $this->_bootstrap->bootstrap('view');
        $view = $this->_bootstrap->getResource('view');

        // save View into registry
        Zend_Registry::getInstance()->view = $view;

        // set the type of docs
        $view->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);

        // set proper paths for view helpers and filters
        $view->addHelperPath(APPLICATION_PATH . '/helpers', 'Helper');
        $view->addHelperPath(FAZEND_PATH . '/View/Helper', 'FaZend_View_Helper');
        $view->addFilterPath(FAZEND_PATH . '/View/Filter', 'FaZend_View_Filter');

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
        $this->_bootstrap->bootstrap('layout');

        // layout reconfigure, if necessary
        $layout = Zend_Layout::getMvcInstance();
        if (!file_exists($layout->getViewScriptPath()))
            $layout->setViewScriptPath(FAZEND_PATH . '/View/layouts/scripts');

        // controller
        $this->_bootstrap->bootstrap('frontController');
        $front = $this->_bootstrap->getResource('frontController');
        $dirs = $front->getControllerDirectory();

        // if (!file_exists($dirs['default']))
        // $front->setControllerDirectory(FAZEND_PATH . 'Controller/controllers/default', 'default');
    }        

    /**
     * Configure routes
     *
     * @return void
     */
    protected function _initRoutes() {
        $this->_bootstrap->bootstrap('frontController');
        $front = $this->_bootstrap->getResource('frontController');

        // configure global routes for all
        $router = new Zend_Controller_Router_Rewrite();

        // routes for custom application operations
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

        // load standard system routes
        $router->addConfig(new Zend_Config_Ini(FAZEND_PATH . '/Application/routes.ini', 'global'), 'routes');

        $front->setRouter($router);
    }    

    /**
     * Configure profiler for development environment
     *
     * @return void
     */
    protected function _initDbProfiler() {
        // profiler is used ONLY in development environment
        if (APPLICATION_ENV === 'production')
            return;

        // maybe there is no DB in the application, sometimes it happens :)
        if (!$this->_bootstrap->hasPluginResource('db'))
            return;

        $this->_bootstrap->bootstrap('db');
        $db = $this->_bootstrap->getResource('db');

        // turn ON the profiler
        $db->setProfiler(true);
    }

    /**
     * Initialize autoloader for Db ActiveRow
     *
     * @return void
     */
    protected function _initDbAutoloader() {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->pushAutoloader(new FaZend_Db_Table_RowLoader(), 'FaZend_Db_Table_ActiveRow_');
        $autoloader->pushAutoloader(new FaZend_Db_TableLoader(), 'FaZend_Db_ActiveTable_');
    }

    /**
     * Initialize cache for tables metadata
     *
     * @return void
     */
    protected function _initTableCache() {
        $cache = Zend_Cache::factory('Core', new FaZend_Cache_Backend_Memory(),
            array(
                'caching' => true,
                'lifetime' => null, // forever 
                'cache_id_prefix' => FaZend_Properties::get()->name . '_' . FaZend_Revision::get(),
                'automatic_serialization' => true
            ),
            array());
             
        // metadata cacher
        // see: http://framework.zend.com/manual/en/zend.db.table.html#zend.db.table.metadata.caching    
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
    }    

    /**
     * Initialize cache for includes
     *
     * @return void
     */
    protected function _initPluginCache() {
        // only in production
        if (APPLICATION_ENV !== 'production')
            return;

        // plugin cache
        // see: http://framework.zend.com/manual/en/zend.loader.pluginloader.html#zend.loader.pluginloader.performance.example
        $classFileIncCache = TEMP_PATH . '/'. FaZend_Properties::get()->name . '-includeCache.php';

        // this may happen if we start from a different process
        if (file_exists($classFileIncCache) && !is_writable($classFileIncCache))
            return;

        if (file_exists($classFileIncCache)) {
            include_once $classFileIncCache;
        }

        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
    }

    /**
     * Initialize logger
     *
     * @return void
     */
    protected function _initLogger() {
        $this->_bootstrap->bootstrap('Email');

        // remove all writers
        FaZend_Log::getInstance()->clean();

        // log errors in ALL environments
        FaZend_Log::getInstance()->addWriter('ErrorLog');
        
        // if testing or development - log into memory as well
        if (APPLICATION_ENV !== 'production')
            FaZend_Log::getInstance()->addWriter('Memory', 'FaZendDebug');
    }

    /**
     * Inject testing elements into the application
     *
     * Will be executed ONLY if it is not a production server
     *
     * @return void
     */
    protected function _initTestInjection() {
        if (APPLICATION_ENV == 'production')
            return;

        $this->_boot('DbAutoloader');
        $this->_boot('DbProfiler');
        $this->_boot('Logger');
        $this->_boot('PluginCache');
        $this->_boot('TableCache');
            
        // make sure it's deployed
        if ($this->_bootstrap->hasPluginResource('deployer'))
            $this->_bootstrap->bootstrap('deployer');

        // objects in 'test/Mocks' directory
        $mocks = APPLICATION_PATH . '/../../test/Mocks';
        if (file_exists($mocks) && is_dir($mocks)) {
            Zend_Loader_Autoloader::getInstance()->registerNamespace('Mocks_');
        }

        // make sure that directory with test is includeable
        set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../../test'),
            get_include_path(),
        )));

        $injectorPhp = APPLICATION_PATH . '/../../test/injector/Injector.php';
        if (!file_exists($injectorPhp))
            return;

        require_once $injectorPhp;
        $injector = new Injector();
        $injector->inject();
    }

}
