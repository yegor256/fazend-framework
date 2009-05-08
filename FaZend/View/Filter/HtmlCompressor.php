<?php
/**
 *
 * Copyright (c) 2009, FaZend.com
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of FaZend.com. located at
 * www.FaZend.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@FaZend.com
 *
 * @copyright Copyright (c) FaZend.com, 2009
 * @version $Id$
 *
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
