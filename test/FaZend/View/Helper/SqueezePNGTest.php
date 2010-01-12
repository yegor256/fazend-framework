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

class FaZend_View_Helper_SqueezePNGTest extends AbstractTestCase {
    
    /**
    * Test PNG rendering
    *
    */
    public function testSqueezePNGWorks () {

        // this page contains html with squeze
        $this->dispatch('/index/squeeze');

        $this->assertNotEquals(false, (bool)$this->getResponse()->getBody(), "Empty HTML instead of page with PNG, why?");

        $this->assertQuery('div[style*="url"]', 'error here: '.$this->getResponse()->getBody());

    }

    /**
    * Test PNG showing
    *
    */
    public function testSqueezePNGShowsActualPNG () {

        $this->dispatch($this->view->url(array('id'=>256), 'squeeze', true));
        $png = $this->getResponse()->getBody();

        $file = tempnam(sys_get_temp_dir(), 'fazend');
        file_put_contents($file, $png);

        $img = imagecreatefrompng($file);

        $this->assertNotEquals(false, $img, 'Image is not valid: '.strlen($png).' bytes in PNG: '.htmlspecialchars($png));

    }

    /**
    * Test image compression
    *
    */
    public function testSqueezeIsCompressedAtItsMaximum () {

        eval ('class Foo extends FaZend_View_Helper_SqueezePNG { function testCompress(array $images) { return $this->_compress($images); } };');
        
        $foo = new Foo();
        $images = array();
        $images = $foo->testCompress($images);

    }

}

