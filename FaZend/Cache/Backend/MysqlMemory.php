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
 * Cache in mysql MEMORY table
 *
 * @package FaZend 
 */
class FaZend_Cache_Backend_MysqlMemory extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface {

    const ROW_TABLE = '__cacherow';
    const TAG_TABLE = '__cachetag';

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

        $row = $this->_getNewCacheRow()->retrieve()
            ->where('label = ?', $id)
            ->fetchRow();

        return $row->data;

    }

    /**
     * Test if a cache is available or not (for the given id)
     *
     * @param  string $id cache id
     * @return mixed|false (a cache is not available) or "last modified" timestamp (int) of the available cache record
     */
    public function test($id) {

        return (bool)$this->_getNewCacheRow()->retrieve()
            ->where('label = ?', $id)
            ->setSilenceIfEmpty()
            ->fetchRow();

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

        // what if it exists already
        $this->remove($id);
        
        // start transaction
        //...

        // create new record for the table
        $cacheRow = $this->_getNewCacheRow();

        // fill it with data
        $cacheRow->label = $label;
        $cacheRow->data = $data;
        $cacheRow->lifetime = $specificLifetime;
        $id = $cacheRow->save();

        // for each tag create a record
        foreach ($tags as $tag) {
            // fill them and save
            $cacheTag = $this->_getNewCacheTag();
            $cacheTag->row = $id;
            $cacheTag->tag = $tag;
            $cacheTag->save();
        }

        // commit transaction
        //..

        return $id;

    }

    /**
     * Remove a cache record
     *
     * @param  string $id Cache id
     * @return boolean True if no problem
     */
    public function remove($id) {

        $this->_getNewCacheRow()->retrieve()
            ->where('label = ?', $id)
            ->fetchRow()
            ->delete();

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

        // we operate with this table
        $table = $this->_getNewCacheRow()->retrieve()->table();

        switch ($mode) {

            case Zend_Cache::CLEANING_MODE_OLD:
                // delete too old elements
                $table->delete('created + lifetime < NOW()');
                break;

            case Zend_Cache::CLEANING_MODE_ALL:
                // delete all data and all tags
                $table->delete('1 = 1');
                 break;

             case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
                $table->getAdapter()->query('delete from ' . self::ROW_TABLE . 
                    ' join ' . self::TAG_TABLE . ' on row = ');

             case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:

             case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                $table->delete('');
        }

    }


}
