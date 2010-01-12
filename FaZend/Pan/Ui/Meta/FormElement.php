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
 * Form element
 *
 * @package UiModeller
 * @subpackage Mockup
 */
abstract class FaZend_Pan_Ui_Meta_FormElement extends FaZend_Pan_Ui_Meta_Abstract {

    /**
     * Field is visible in TWO columns (name of field, field)
     *
     * @var boolean
     */
    protected $_alignedStyle = true;

    /**
     * Set the style of form, an aligned one
     *
     * @return this
     */
    public function setAlignedStyle($style = true) {
        $this->_alignedStyle = $style;
        return $this;
    }
    
}
