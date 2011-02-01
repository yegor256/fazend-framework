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
class FaZend_Application_Resource_fz_caches extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Initializes the resource.
     * @return void
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init()
    {
        //@todo when this ticket is resolved: http://framework.zend.com/issues/browse/ZF-8991
        $cache = Zend_Cache::factory(
            'Core',
            new FaZend_Cache_Backend_Memory(),
            array(
                'caching' => true,
                'lifetime' => null, // forever
                'cache_id_prefix' => FaZend_Revision::getName() . '_' . FaZend_Revision::get(),
                'automatic_serialization' => true
            ),
            array()
        );

        // metadata cacher
        // see: http://framework.zend.com/manual/en/zend.db.table.html#zend.db.table.metadata.caching
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

        // only in production
        if (APPLICATION_ENV !== 'production') {
            return;
        }

        // plugin cache
        // see: http://framework.zend.com/manual/en/zend.loader.pluginloader.html
        $phpFile = TEMP_PATH . '/'
            . FaZend_Revision::getName() . '-r'
            . FaZend_Revision::get() . '-includeCache.php';

        if (file_exists($phpFile)) {
            include_once $phpFile;
        }

        // set cache for "included" files
        Zend_Loader_PluginLoader::setIncludeFileCache($phpFile);
    }

}
