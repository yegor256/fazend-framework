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

/**
 *
 *
 * @category   FaZend
 * @package    FaZend_View_Filter
 */
class FaZend_View_Filter_CssCompressor implements Zend_Filter_Interface {

        /**
         * Defined by Zend_Filter_Interface
         *
         * Compress CSS into a long string
         *
         * @param  string $value
         * @return string
         */
        public function filter($css) {

		return preg_replace(array(
			'/[\n\r\t]/',
			'/\s+([\,\:\{\}\.])/s',
			'/([\,\;\:\{\}])\s+/s',
			'/\/\*.*?\*\//'
		), array(
			' ',
			'${1}', 
			'${1}', 
			''
		), $css);

        }

}
