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
 * Zend_Date decorator
 *
 * @package Date
 */
class FaZend_Date extends Zend_Date
{

    /**
     * Create Zend_Date object on-fly
     *
     * @param sting|mixed
     * @return FaZend_Date
     */
    public static function make($time)
    {
        return new FaZend_Date($time);
    }

    /**
     * Current date is between these two dates (including them!)?
     *
     * @param Zend_Date|mixed Start date
     * @param Zend_Date|mixed End date
     * @return boolean
     **/
    public function isBetween($start, $end)
    {
        return (($this->isEarlier($end) || $this->equals($end)) && 
            ($this->isLater($start) || $this->equals($start)));
    }

}
