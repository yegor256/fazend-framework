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
 * String DOESN'T start with something
 *
 * @package FaZend
 */
class FaZend_Validate_NotStartWith extends Zend_Validate_Abstract {

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
    public function __construct($prefix) {
        $this->setPrefix($prefix);
    }

    /**
     * Returns the prefix
     *
     * @return string
     */
    public function getPrefix() {
        return $this->_prefix;
    }

    /**
     * Sets the prefix
     *
     * @param  string The prefix
     * @return Zend_Validate_Regex Provides a fluent interface
     */
    public function setPrefix($prefix) {
        $this->_prefix = (string) $prefix;
        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is instance of some class
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value) {
        if (strpos($value, $this->_prefix) === 0) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
