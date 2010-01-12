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

class FaZend_View_Filter_HtmlCompressorTest extends AbstractTestCase {
    
    /**
    * Provides HTML samples
    *
    * @return array
    */
    public static function providerHtml () {
        return array(
            
            // pre formatting should NOT be touched
            array(
                "<html><body><pre style='border:0'>line1\nline2</pre></body></html>",
                "<html><body><pre style='border:0'>line1\nline2</pre></body></html>"
            ),

            // all spaces between tags should be killed
            array(
                '<html>  <body>   </body>  </html>',
                '<html>  <body>  </body>  </html>'
            ),
            
            // all \n\r\t should be killed as well
            array(
                "<html>\t<body>\nworks?</body>\r</html>  \n",
                '<html> <body> works?</body> </html>'
            ),

            // kill comments properly
            array(
                '<html><script><!-- test --></script><body><style><!-- ff --></style><!-- to kill --></body></html>',
                '<html><script><!-- test --></script><body><style><!-- ff --></style></body></html>',
            ),
            
        );
    }
    
    /**
    * Test all possible scenarios of compression
    *
    * @dataProvider providerHtml
    */
    public function testCompressesFine ($html, $result) {
        $compressor = new FaZend_View_Filter_HtmlCompressor();
        $new = $compressor->filter($html);
        $this->assertEquals($result, $new, "Incorrect HTML compression of [{$html}], got this: [{$new}], expected: [{$result}]");
    }

}
