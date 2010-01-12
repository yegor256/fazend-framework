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
 * One simple validator for numeric values
 *
 * @package Validate
 */
class FaZend_Validate_Numeric extends Zend_Validate_Abstract {

    const INVALID = 'invalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "NON-NUMERIC value given, instead of a number",
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a numeric value. This is analoguos for
     * a direct call to is_numeric().
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value) {
        if (!is_numeric($value)) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
