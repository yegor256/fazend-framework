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

require_once 'Zend/Filter/Interface.php';

/**
 * CSS compression
 *
 * @package View
 * @subpackage Filter
 */
class FaZend_View_Filter_CssCompressor implements Zend_Filter_Interface {

    /**
     * Regexp replacement patterns
     *
     * @var array
     */
    protected static $_replacer = array(
        '/[\n\r\t]+/' => ' ', // remove duplicated white spaces
        '/\s+/' => ' ', // convert multiple spaces to single
        '/\s+([\,\:\{\}])/' => '${1}', // compress leading white spaces
        '/([\,\;\:\{\}])\s+/' => '${1}', // compress trailing white spaces
        '/\/\*.*?\*\//' => '', // kill comments at all
    );

    /**
     * Defined by Zend_Filter_Interface
     *
     * Compress CSS into a long string
     *
     * @param string CSS content to be compressed
     * @return string
     */
    public function filter($css) {
        return preg_replace(array_keys(self::$_replacer), self::$_replacer, $css);
    }

}
