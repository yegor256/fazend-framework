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
