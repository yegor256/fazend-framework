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
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 */
class FaZend_View_Filter_HtmlCompressor implements Zend_Filter_Interface {

        /**
         * Defined by Zend_Filter_Interface
         *
         * Compress HTML into a long string
         *
         * @param  string $value
         * @return string
         */
        public function filter($value) {
		return trim(preg_replace(array(
			'/[\n\r\t]/',
			'/\s+/',
			'/\>\s+\</',
			'/\s\/\>/',
		), array(
			' ',
			' ',
			'><',
			'/>',
		), $value));
        }

}
