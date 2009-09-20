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
 * Validate if it's subject equals to true?
 *
 * @package FaZend
 */
class FaZend_Validator_True extends FaZend_Validator_Abstract {

    /**
     * Validator
     *
     * @return boolean
     */
    public function validate() {
        return (bool)$this->_subject;
    }

}
