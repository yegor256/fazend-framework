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
 * Cache in memory
 *
 * @package FaZend 
 */
class FaZend_Cache_Backend_Memory extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface {

    /**
     * Cache in an associative array
     *
     * @var array
     */
    protected $_cache = array();

    /**
     * Set the frontend directives
     *
     * @param array $directives assoc of directives
     */
    public function setDirectives($directives) {
        $this->_directives = $directives;
    }

    /**
     * Test if a cache is available for the given id and (if yes) return it (false else)
     *
     * Note : return value is always "string" (unserialization is done by the core not by the backend)
     *
     * @param  string  $id             Cache id
     * @param  boolean $doNotTestCacheValidity If set to true, the cache validity won't be tested
     * @return string|false cached datas
     */
    public function load($id, $doNotTestCacheValidity = false) {

        if (isset($this->_cache[$id]))
            return $this->_cache[$id];
        return false;

    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id cache id
     * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id) {

        return isset($this->_cache[$id]);

    }

    /**
     * Save some string datas into a cache record
     *
     * Note : $data is always "string" (serialization is done by the
     * core not by the backend)
     *
     * @param  string $data        Datas to cache
     * @param  string $label          Cache id
     * @param  array $tags         Array of strings, the cache record will be tagged by each string entry
     * @param  int   $specificLifetime If != false, set a specific lifetime for this cache record (null => infinite lifetime)
     * @return boolean true if no problem
     */
    public function save($data, $label, $tags = array(), $specificLifetime = false) {

        $this->_cache[$label] = $data;

    }

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id) {

        unset($this->_cache[$id]);

    }

    /**
     * Clean some cache records
     *
     * Available modes are :
     * Zend_Cache::CLEANING_MODE_ALL (default)    => remove all cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_OLD          => remove too old cache entries ($tags is not used)
     * Zend_Cache::CLEANING_MODE_MATCHING_TAG     => remove cache entries matching all given tags
     *                           ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => remove cache entries not {matching one of the given tags}
     *                           ($tags can be an array of strings or a single string)
     * Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG => remove cache entries matching any given tags
     *                           ($tags can be an array of strings or a single string)
     *
     * @param  string $mode Clean mode
     * @param  array  $tags Array of tags
     * @return boolean true if no problem
     */
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array()) {

        switch ($mode) {

            case Zend_Cache::CLEANING_MODE_OLD:
                // delete too old elements
                break;

            case Zend_Cache::CLEANING_MODE_ALL:
                // delete all data and all tags
                $this->_cache = array();
                break;

             case Zend_Cache::CLEANING_MODE_MATCHING_TAG:

             case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:

             case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                 break;
        }

    }


}
