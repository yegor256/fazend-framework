<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_RevisionTest extends AbstractTestCase
{
    
    public function testWeCanGetRevisionNumber()
    {
        $revision = FaZend_Revision::get();
        $this->assertNotEquals(false, $revision);
        logg('Rev: ' . $revision);
    }

}
