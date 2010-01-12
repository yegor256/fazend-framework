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

require_once 'AbstractTestCase.php';

class FaZend_Cache_Backend_MysqlMemoryTest extends AbstractTestCase {
    
    public function testCachingWorks () {

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
        