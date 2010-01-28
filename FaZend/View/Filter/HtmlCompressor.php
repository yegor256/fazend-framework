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

require_once 'Zend/Filter/Interface.php';

/**
 * Html compressor
 *
 * @package View
 * @subpackage Filter
 */
class FaZend_View_Filter_HtmlCompressor implements Zend_Filter_Interface
{
    
    const MASK_PREFIX = 'mask-';
    
    /**
     * List of regex for replacements
     *
     * I don't like that I have to comment these three lines, but I can't
     * find why this conversion SOMETIMES produce invalid layout in some
     * browsers.
     *
     * @var array
     */
    protected static $_replacer = array(
        '/[\n\r\t]+/' => ' ', // convert spacers into normal spaces
        '/\s{2,}/' => '  ', // convert two-or-more spaces into two spaces
        '/\s\/\>/' => '/>', // remove spaces between tag closing bracket
        '/\<\!\-\-.*?\-\-\>/' => '', // remove comments

        // '/\s+/' => ' ', // convert multiple spaces to single
        // '/\>\s+/' => '>', // remove spaces after tags
        // '/\s+\</' => '<', // remove spaces before tags
    );

    /**
     * Defined by Zend_Filter_Interface
     *
     * Compress HTML into a long string
     *
     * @param string HTML content to be compressed
     * @return string
     */
    public function filter($html)
    {
        // we DON'T touch contect in these tags
        $tagsToMask = array(
            'pre', 
            'script', 
            'style', 
            'textarea');

        // convert masked tags
        $masked = array();
        foreach ($tagsToMask as $tag) {
            $matches = array();
            preg_match_all('/\<' . $tag . '(.*?)\>(.*?)\<\/' . $tag . '\>/msi', $html, $matches);
            foreach ($matches[0] as $id=>$match) {
                $html = str_replace($match, self::MASK_PREFIX . $tag . $id, $html);
                $masked[$tag . $id] = $match;
            }
        }   

        // compress HTML
        $html = trim(preg_replace(array_keys(self::$_replacer), self::$_replacer, $html));

        // deconvert masked tags from
        preg_match_all('/' . preg_quote(self::MASK_PREFIX, '/'). '(\w+\d+)/', $html, $matches);
        foreach ($matches[0] as $id=>$match)
            if (isset($masked[$matches[1][$id]]))
                $html = str_replace($match, $masked[$matches[1][$id]], $html); 

        return $html;
    }

}
