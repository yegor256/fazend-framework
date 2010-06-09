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

require_once 'FaZend/View/Helper/Forma/Field.php';

/**
 * Text field
 *
 * @package Model_Form
 */
class FaZend_View_Helper_Forma_FieldText extends FaZend_View_Helper_Forma_Field
{

    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    protected function _getFormElement($name)
    {
        if (isset($this->_attribs['type']) && ($this->_attribs['type'] == 'password')) {
            unset($this->_attribs['type']);
            return new Zend_Form_Element_Password($name);
        }
        return new Zend_Form_Element_Text($name);
    }

}
