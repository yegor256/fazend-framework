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
 * Validate by regexp
 *
 * @package FaZend
 */
class FaZend_Validator_Regexp extends FaZend_Validator_Abstract {

    /**
     * Validate by regular expression
     *
     * @param string Regular expression
     * @return boolean
     */
    public function validate($regexp) {
        return preg_match($regexp, $this->_subject);
    }

}
