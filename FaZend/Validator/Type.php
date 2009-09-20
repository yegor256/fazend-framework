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
 * Validate type
 *
 * @package FaZend
 */
class FaZend_Validator_Type extends FaZend_Validator_Abstract {

    /**
     * Validator
     *
     * @param string Type name
     * @return boolean
     * @throws FaZend_Validator_Type_InvalidType
     */
    public function validate($type) {
        switch (strtolower($type)) {
            case 'string':
                return is_string($this->_subject);
                
            case 'integer':
            case 'int':
                return is_integer($this->_subject);
                
            case 'bool':
            case 'boolean':
                return is_bool($this->_subject);
                
            default:
                FaZend_Exception::raise('FaZend_Validator_Type_InvalidType',
                    "Type '{$type}' is unknown");
        }
    }

}
