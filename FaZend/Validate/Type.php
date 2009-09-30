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
 * Object is of some type
 *
 * @package FaZend
 */
class FaZend_Validate_Type extends Zend_Validate_Abstract {

    const INVALID = 'invalid';
    const WRONG_TYPE = 'wrongType';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "Object is not of type '%type%'",
        self::WRONG_TYPE   => "Type '%type%' is unknown for this validator",
    );

    /**
     * @var array
     */
    protected $_messageVariables = array(
        'type' => '_type'
    );

    /**
     * Name of the type class/iface
     *
     * @var string
     */
    protected $_type;

    /**
     * Sets validator options
     *
     * @param  string Name of the type class/iface
     * @return void
     */
    public function __construct($type) {
        $this->setType($type);
    }

    /**
     * Returns the type
     *
     * @return string
     */
    public function getType() {
        return $this->_type;
    }

    /**
     * Sets the type
     *
     * @param  string The type
     * @return Zend_Validate_Regex Provides a fluent interface
     */
    public function setType($type) {
        $this->_type = (string) $type;
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
        switch (strtolower($this->_type)) {
            
            case 'array':
                $ok = is_array($value);
                break;
            
            case 'string':
            case 'str':
                $ok = is_string($value);
                break;
            
            case 'int':
            case 'integer':
                $ok = is_int($value);
                break;
                
            case 'bool':
            case 'boolean':
                $ok = is_boolean($value);
                break;
        }
        
        if (!isset($ok)) {
            $this->_error(self::WRONG_TYPE);
            return false;
        }
                
        if (!$ok) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
