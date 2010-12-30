<?php
/**
 * @version $Id$
 */

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_SqueezePNGTest extends AbstractTestCase
{

    public function testSqueezePNGWorks()
    {
        // this page contains html with squeze
        $this->dispatch('/index/squeeze');
        $this->assertNotRedirect();
        $this->assertController('index');
        $this->assertAction('squeeze');
        $this->assertNotEquals(
            false,
            (bool)$this->getResponse()->getBody(),
            "Empty HTML instead of page with PNG, why?"
        );
        $this->assertQuery('div[style*="url"]', 'error here: ' . $this->getResponse()->getBody());
    }

    public function testSqueezePNGShowsActualPNG()
    {
        $this->dispatch($this->view->url(array('id'=>256), 'fz__squeeze', true));
        $png = $this->getResponse()->getBody();

        $file = tempnam(sys_get_temp_dir(), 'fazend');
        file_put_contents($file, $png);

        $img = imagecreatefrompng($file);
        $this->assertNotEquals(
            false,
            $img,
            'Image is not valid: ' . strlen($png) . ' bytes in PNG: ' . htmlspecialchars($png)
        );
    }

    public function testSqueezeIsCompressedAtItsMaximum()
    {
        eval (
            '
            class SqueezeFoo extends FaZend_View_Helper_SqueezePNG
            {
                function testCompress(array $images)
                {
                    return $this->_compress($images);
                }
            };
            '
        );

        $foo = new SqueezeFoo();
        $images = array();
        $images = $foo->testCompress($images);
    }

}

