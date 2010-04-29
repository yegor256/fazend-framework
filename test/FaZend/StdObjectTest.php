<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_StdObjectTest extends AbstractTestCase
{
    
    public function testSmartGetterWorks()
    {
        $obj = new Model_StdObject();
        $this->assertEquals('name', $obj->name);
    }

}
