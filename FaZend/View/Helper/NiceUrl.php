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
 * Converts string to nice URL string
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_NiceUrl
{

    const DELIMITER = '-';

    /**
     * Converts and returns
     *
     * @param string Parameter for URL
     * @param string Suffix to add, if necessary
     * @return string
     * @todo How about UTF-8?
     */
    public function niceUrl($str, $suffix = '.html')
    {
        // replace anything strange, and compress it into a nice URL
        return trim(
            preg_replace(
                '/' . preg_quote(self::DELIMITER). '+/', 
                self::DELIMITER, 
                preg_replace(
                    '/[^\w\d]/', 
                    self::DELIMITER, 
                    ucwords($str)
                )
            ), 
            self::DELIMITER
        ) . $suffix;
    }

}
