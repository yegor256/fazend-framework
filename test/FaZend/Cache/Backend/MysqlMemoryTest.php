<?php

require_once 'AbstractTestCase.php';

class FaZend_Cache_Backend_MysqlMemoryTest extends AbstractTestCase
{
    
    public function testCachingWorks()
    {
        /*
        $cache = Zend_Cache::factory('Core', 'FaZend_Cache_Backend_MysqlMemory', array(
            'caching' => true,
            'cache_id_prefix' => 'panel',
            'lifetime' => null,
            'automatic_serialization' => true,
            'automatic_cleaning_factor' => 100,
            'write_control' => true,
            'ignore_user_abort' => true), array(), false, true);

        $data = str_repeat('test data', rand(100, 999));    
        $id = str_repeat(rand(0,9), 100);
        $tags = array('tag1', 'tag2');

        $this->assertNotEquals(false, $cache->save($data, $id, $tags));

        $this->assertNotEquals(false, $cache, "Can't create cache, why?");
        */
    }

}
        