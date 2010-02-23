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
 * @version $Id: Money.php 1587 2010-02-07 07:49:26Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Abstract business object
 *
 * @package Bo
 */
abstract class FaZend_Bo_Abstract
{
    
    /**
     * Convert it to string
     *
     * @return string
     */
    abstract public function __toString();

    /**
     * Get certain parts of the class
     *
     * @param string Part to get, property
     * @return string
     */
    abstract public function __get($part);
    
    /**
     * Set value
     *
     * @param mixed Value
     * @param string Part name
     * @return void
     */
    abstract public function set($value, $part = null);

    /**
     * Get value, or part of it
     *
     * @param string Part name
     * @return mixed
     */
    abstract public function get($part = null);

}
