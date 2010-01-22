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
 * Produce a random string
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_LoremIpsum
{

    /**
     * List of stupid texts
     *
     * @var string[]
     */
    protected static $_loremIpsumSlices = array(
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
        'Nunc convallis nulla id eros interdum aliquam',
        'Nullam in elementum enim',
        'Nam adipiscing hendrerit enim a gravida',
        'Proin ligula tellus, sagittis vitae malesuada vel, dignissim cursus tellus',
        'Vivamus aliquam rhoncus dolor quis imperdiet',
        'Ut sit amet dui vel ligula auctor eleifend',
        'Cras mauris nisl, porta ac vulputate at, adipiscing nec erat.'
    );

    /**
     * Produces a random line
     *
     * @param int Total amount of letters to show
     * @return string
     */
    public function loremIpsum($length = 300)
    {
        return self::getLoremIpsum($length);
    }

    /**
     * Lorem ipsum
     *
     * @param int Total amount of letters to show
     * @return string
     */
    public static function getLoremIpsum($length = 300)
    {
        shuffle(self::$_loremIpsumSlices);
        return cutLongLine(trim(implode('. ', self::$_loremIpsumSlices)), $length);
    }

}
