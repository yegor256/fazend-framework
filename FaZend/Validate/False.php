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
 * One simple validator for FALSE value
 *
 * @package Validate
 */
class FaZend_Validate_False extends Zend_Validate_Abstract {

    const INVALID = 'invalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID   => "True value given, instead of FALSE",
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is FALSE
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value) {
        if ($value) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
