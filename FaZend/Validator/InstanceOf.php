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
 * Validate if the subject is instance of certain class/interface
 *
 * @package FaZend
 */
class FaZend_Validator_InstanceOf extends FaZend_Validator_Abstract {

    /**
     * Validator
     *
     * @param string Class/interface name
     * @return boolean
     */
    public function validate($type) {
        return $this->_subject instanceof $type;
    }

}
