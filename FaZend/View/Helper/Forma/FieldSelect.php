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
 * Select field
 *
 * @package Model_Form
 */
class FaZend_View_Helper_Forma_FieldSelect extends FaZend_View_Helper_Forma_Field {

    /**
     * List of options
     *
     * @var array
     */
    protected $_options;
    
    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    protected function _getFormElement($name) {
        return new Zend_Form_Element_Select($name);
    }

    /**
     * Configure form element
     *
     * @param Zend_Form_Element The element to configure
     * @return void
     */
    protected function _configureFormElement(Zend_Form_Element $element) {
        parent::_configureFormElement($element);
        // $element->setAttrib('class', 'btn');

        $element->setMultiOptions($this->_options);
    }

    /**
     * Set list of options
     *
     * @param array Associative array of options
     * @param boolean Shall we sort the options?
     * @return void
     */
    protected function _setOptions(array $options, $sort = false) {
        $this->_options = $options;
        if ($sort)
            asort($this->_options);
    }

}
