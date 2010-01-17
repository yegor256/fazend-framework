<?php

require_once 'AbstractTestCase.php';

class FaZend_Pan_Database_MapTest extends AbstractTestCase
{

    public function testPngBuilderWorks()
    {
        $map = new FaZend_Pan_Database_Map();
        $this->assertNotEquals(false, $map->png());
    }

}
        