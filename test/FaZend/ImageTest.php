<?php

require_once 'AbstractTestCase.php';

class FaZend_ImageTest extends AbstractTestCase
{
    
    public function testImageCreationWorks()
    {
        $image = new FaZend_Image();
        $image->setDimensions(300, 300);

        $image->imagerectangle(10, 10, 50, 50, $image->getColor('border'));
        $this->assertNotEquals(false, $image->png());
    }

}
