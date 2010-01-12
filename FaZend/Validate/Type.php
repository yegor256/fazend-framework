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
 * Object is of some type
 *
 * @package Validate
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
     * Returns true if and only if $value is of some type. For example:
     *
     * <code>
     * validate()
     *    ->type($value1, 'string') // make sure that $value1 is of type STRING
     *    ->type($value2, 'int') // make sure $value2 is INTEGER
     *    ->type($value3, 'bool'); // make sure that it's BOOLEAN
     * <code>
     *
     * @param mixed $value
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
                $ok = is_bool($value);
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
