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
 * Html compressor
 *
 * @package View
 * @subpackage Filter
 */
class FaZend_View_Filter_HtmlCompressor implements Zend_Filter_Interface {
    
    /**
     * List of regex for replacements
     *
     * @var array
     */
    protected static $_replacer = array(
        '/[\n\r\t]+/' => ' ', // convert spacers into normal spaces
        '/\s+/' => ' ', // convert multiple spaces to single
        '/\>\s+/' => '>', // remove spaces after tags
        '/\s+\</' => '<', // remove spaces before tags
        '/\s\/\>/' => '/>', // remove spaces between tag closing bracket
        '/\<\!\-\-.*?\-\-\>/' => '', // remove comments
    );

    /**
     * Defined by Zend_Filter_Interface
     *
     * Compress HTML into a long string
     *
     * @param string HTML content to be compressed
     * @return string
     */
    public function filter($html) {
        // we DON'T touch contect in these tags
        $masked = array(
            'pre', 
            'script', 
            'style', 
            'textarea');

        // convert masked tags into BASE64 form
        foreach($masked as $tag) {
            $matches = array();
            preg_match_all('/\<' . $tag . '(.*?)\>(.*?)\<\/' . $tag . '\>/msi', $html, $matches);
            foreach ($matches[0] as $id=>$match)
                $html = str_replace($match, "<{$tag}{$matches[1][$id]}>" . base64_encode($matches[2][$id]) . "</{$tag}>", $html);
        }    

        // compress HTML
        $html = trim(preg_replace(array_keys(self::$_replacer), self::$_replacer, $html));

        // deconvert masked tags from BASE64
        foreach($masked as $tag) {
            preg_match_all('/\<' . $tag . '(.*?)\>(.*?)\<\/' . $tag . '\>/msi', $html, $matches);
            foreach ($matches[0] as $id=>$match)
                $html = str_replace($match, "<{$tag}{$matches[1][$id]}>" . base64_decode($matches[2][$id]) . "</{$tag}>", $html);
        }    

        return $html;
    }

}
