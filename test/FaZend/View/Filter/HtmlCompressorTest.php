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
				'<html><body></body></html>'
			),
			
			// all \n\r\t should be killed as well
			array(
				"<html>\t<body>\nworks?</body>\r</html>  \n",
				'<html><body> works?</body></html>'
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
		$this->assertEquals ($result, $new, "Incorrect HTML compression of [{$html}], got this: [{$new}]");

	}

}
