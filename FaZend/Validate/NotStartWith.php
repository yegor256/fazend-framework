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
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * String DOESN'T start with something
 *
 * @package Validate
 */
class FaZend_Validate_NotStartWith extends Zend_Validate_Abstract
{

    const INVALID = 'invalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "String starts with '%prefix%'",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'prefix' => '_prefix'
    );

    /**
     * Name of the prefix class/iface
     *
     * @var string
     */
    protected $_prefix;

    /**
     * Sets validator options
     *
     * @param  string Name of the prefix class/iface
     * @return void
     */
    public function __construct($prefix)
    {
        $this->setPrefix($prefix);
    }

    /**
     * Returns the prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->_prefix;
    }

    /**
     * Sets the prefix
     *
     * @param  string The prefix
     * @return Zend_Validate_Regex Provides a fluent interface
     */
    public function setPrefix($prefix)
    {
        $this->_prefix = (string) $prefix;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value NOT starts with this predefined prefix. For example
     * 'abcTest' will return FALSE with notStartWith($value, 'abc').
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if (strpos($value, $this->_prefix) === 0) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
