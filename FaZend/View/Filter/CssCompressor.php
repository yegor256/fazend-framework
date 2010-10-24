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

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * CSS compression.
 *
 * @package View
 * @subpackage Filter
 */
class FaZend_View_Filter_CssCompressor implements Zend_Filter_Interface
{

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
     * Compress CSS into a long string.
     *
     * @param string CSS content to be compressed
     * @return string
     */
    public function filter($css)
    {
        if (strlen($css) > ini_get('pcre.backtrack_limit')) {
            FaZend_Exception::raise(
                'FaZend_View_Filter_CssCompressor_Exception',
                "You should raise pcre.backtrack_limit for large CSS pages compression"
            );
        }

        return preg_replace(
            array_keys(self::$_replacer),
            self::$_replacer,
            $css
        );
    }

}
