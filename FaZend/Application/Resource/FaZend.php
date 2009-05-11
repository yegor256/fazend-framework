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

/**
 * Resource for initializing FaZend framework
 *
 * @uses       Zend_Application_Resource_Base
 * @category   FaZend
 * @package    FaZend_Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_FaZend extends Zend_Application_Resource_ResourceAbstract {

	/**
	* Defined by Zend_Application_Resource_Resource
	*
	* @return boolean
	*/
	public function init() {

		$options = $this->getOptions();

		if (!isset($options['name']))
			throw new Exception("FaZend.name should be defined in your app.ini file");

		$this->_initTableCache($options);

		$this->_initPluginCache($options);

		$this->_initAutoloaders($options);

		if (isset($options['Db']))
			$this->_initDbFactory($options['Db']);
	}

	/**
	* Initialize cache for tables
	*
	* @return void
	*/
	protected function _initTableCache($options) {

		$cache = Zend_Cache::factory('Core', 'File',
			array(
				'caching' => true,
				'lifetime' => 60 * 60 * 24, // 1 day
				'cache_id_prefix' => $options['name'],
				'automatic_serialization' => true

			),
			array(
				'cache_dir' => sys_get_temp_dir(),
			));
		 	
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

		// plugin cache
		// see: http://framework.zend.com/manual/en/zend.loader.pluginloader.html#zend.loader.pluginloader.performance.example
		$classFileIncCache = sys_get_temp_dir() . '/'.$options['name'].'-includeCache.php';

		if (file_exists($classFileIncCache)) {
			include_once $classFileIncCache;
		}

		Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);

	}

	/**
	* Initialize autoloader for Model
	*
	* @return void
	*/
	protected function _initAutoloaders($options) {

	    	$autoloader = Zend_Loader_Autoloader::getInstance();
	    	
	    	// for all our classes
	    	$autoloader->registerNamespace('FaZend_');

	    	// load system classes properly
	    	if (isset($options['namespaces'])) {
	    		if (!is_array($options['namespaces']))
	    			$options['namespaces'] = array($options['namespaces']);

		    	foreach($options['namespaces'] as $namespace)
		    		$autoloader->registerNamespace($namespace.'_');
		}	

	}	

	/**
	* Initialize database tables
	*
	* @return void
	*/
	protected function _initDbFactory($options) {

	    	foreach($options as $table) {
	    		FaZend_DbFactory::create($table);
	    	}	
	    		        
	}	

}
