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
 * @version $Id: Money.php 1748 2010-03-23 13:05:34Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * @see FaZend_Bo_Abstract
 */
require_once 'FaZend/Bo/Abstract.php';

/**
 * IP address
 *
 * Use it like this:
 *
 * <code>
 * $ip = new FaZend_Bo_Ip('127.0.0.1');
 * </code>
 *
 * @package Bo
 */
class FaZend_Bo_Ip extends FaZend_Bo_Abstract
{

    /**
     * IP address
     * 
     * @var string
     */
    protected $_ip;
    
    /**
     * Construct
     * 
     * @param string IP address
     * @return void
     */
    public function __construct($ip)
    {
        validate()->ip(
            $ip, 
            "Wrong IP format: '{$ip}'"
        );
        $this->_ip = (string)$ip;
    }
    
    /**
     * Convert to String
     * 
     * @return string
     */
    public function __toString()
    {
        return (string)$this->_ip;
    }
    
    /**
     * Static Construct
     * 
     * @param string IP address
     * @return FaZend_Bo_Ip
     */
    public static function create($ip)
    {
        return new FaZend_Bo_Ip($ip);
    }
    
    /**
     * Get value, or part of it
     *
     * @param string Part name
     * @return mixed
     */
    public function get($part = null)
    {
        return (string)$this->_ip;
    }
    
    /**
     * Set value
     *
     * @param mixed Value
     * @param string Part name
     * @return void
     */
    public function set($value, $part = null)
    {
        $this->_ip = (string)$value;
    }
    
    /**
     * Get certain parts of the class
     *
     * @param string Part to get, property
     * @return string
     */    
    public function __get($name)
    {
        return $this->get($name);
    }
    
}
