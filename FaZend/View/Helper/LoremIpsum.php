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
 * Produce a random string
 *
 * @package FaZend 
 */
class FaZend_View_Helper_LoremIpsum {

    /**
     * Produces a random line
     *
     * @param int Total amount of letters to show
     * @return string
     */
    public function loremIpsum($length = 300) {
        return self::getLoremIpsum($length);
    }

    /**
     * Lorem ipsum
     *
     * @param int Total amount of letters to show
     * @return string
     */
    public static function getLoremIpsum($length = 300) {

        shuffle(self::$_loremIpsumSlices);

        return cutLongLine(trim(implode('. ', self::$_loremIpsumSlices)), $length);

    }
                        

    /**
     * List of stupid texts
     *
     * @var string[]
     */
    private static $_loremIpsumSlices = array(
        'Lorem ipsum dolor sit amet, consectetur adipiscing elit',
        'Nunc convallis nulla id eros interdum aliquam',
        'Nullam in elementum enim',
        'Nam adipiscing hendrerit enim a gravida',
        'Proin ligula tellus, sagittis vitae malesuada vel, dignissim cursus tellus',
        'Vivamus aliquam rhoncus dolor quis imperdiet',
        'Ut sit amet dui vel ligula auctor eleifend',
        'Cras mauris nisl, porta ac vulputate at, adipiscing nec erat.');

}
