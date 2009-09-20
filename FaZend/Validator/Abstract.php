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
 * Validator, abstract
 *
 * @package FaZend
 */
class FaZend_Validator_Abstract {

    /**
     * Default message to show
     *
     * @var string
     */
    public $defaultMessage = 'Validation failed';

    /**
     * The subject to validate
     *
     * @var mixed
     */
    protected $_subject;

    /**
     * Public constructor
     *
     * @param mixed The subject to validate
     * @return void
     */
    public function __construct($subject) {
        $this->_subject = $subject;
    }

}
