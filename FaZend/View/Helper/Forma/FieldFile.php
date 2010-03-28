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
 * File uploading field
 *
 * @package helpers
 */
class FaZend_View_Helper_Forma_FieldFile extends FaZend_View_Helper_Forma_Field
{

    /**
     * Get value from the form element
     *
     * @param Zend_Form_Element The element to work with
     * @return mixed
     */
    public function deriveValue(Zend_Form_Element $element)
    {
        if ($element->isReceived()) {
            return $element->getFileName();
        }
        return null;
    }

    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    protected function _getFormElement($name)
    {
        return new Zend_Form_Element_File($name);
    }

    /**
     * Configure form element
     *
     * @param Zend_Form_Element The element to configure
     * @return void
     */
    protected function _configureFormElement(Zend_Form_Element $element)
    {
        parent::_configureFormElement($element);
        $element
            ->setDecorators(array())
            ->addDecorator('File')
            ->addDecorator('HtmlTag', array('tag'=>'dd'))
            ->addDecorator('Label', array('escape' => false));
    }

}
