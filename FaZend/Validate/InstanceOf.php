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
 * Object is instance of some class/interface
 *
 * @package Validate
 */
class FaZend_Validate_InstanceOf extends Zend_Validate_Abstract {

    const INVALID = 'invalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Object is not an instance of class/interface '%parent%'",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'parent' => '_parent'
    );

    /**
     * Name of the parent class/iface
     *
     * @var string
     */
    protected $_parent;

    /**
     * Sets validator options
     *
     * @param  string Name of the parent class/iface
     * @return void
     */
    public function __construct($parent) {
        $this->setParent($parent);
    }

    /**
     * Returns the parent
     *
     * @return string
     */
    public function getParent() {
        return $this->_parent;
    }

    /**
     * Sets the parent
     *
     * @param  string The parent
     * @return Zend_Validate_Regex Provides a fluent interface
     */
    public function setParent($parent) {
        $this->_parent = (string) $parent;
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
        if (!($value instanceof $this->_parent)) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
