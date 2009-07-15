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
 * @uses       Zend_Application_Resource_Base
 * @category   FaZend
 * @package    FaZend_Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_Fazend extends Zend_Application_Resource_ResourceAbstract {

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return boolean
     */
    public function init() {

        $options = $this->getOptions();

        if (!isset($options['name']))
            throw new Exception("[FaZend.name] should be defined in your app.ini file");

        $this->_initTableCache($options);

        $this->_initPluginCache($options);

        $config = new Zend_Config($options);
        FaZend_Properties::setOptions($config);

        return $config;
    }

    /**
     * Initialize cache for tables
     *
     * @return void
     */
    protected function _initTableCache($options) {

        $cache = Zend_Cache::factory('Core', new FaZend_Cache_Backend_Memory(),
            array(
                'caching' => true,
                'lifetime' => null, // forever 
                'cache_id_prefix' => $options['name'] . '_' . FaZend_Revision::get(),
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
    protected function _initPluginCache($options) {

        // only in production
        if (APPLICATION_ENV !== 'production')
            return;

        // plugin cache
        // see: http://framework.zend.com/manual/en/zend.loader.pluginloader.html#zend.loader.pluginloader.performance.example
        $classFileIncCache = sys_get_temp_dir() . '/'.$options['name'].'-includeCache.php';

        // this may happen if we start from a different process
        if (file_exists($classFileIncCache) && !is_writable($classFileIncCache))
            return;

        if (file_exists($classFileIncCache)) {
            include_once $classFileIncCache;
        }

        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);

    }

}
