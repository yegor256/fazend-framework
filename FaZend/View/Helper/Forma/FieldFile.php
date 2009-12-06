<?php
/**
 *
 * Copyright (c) 2008, TechnoPark Corp., Florida, USA
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of TechnoPark Corp. located at
 * www.technoparkcorp.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@technoparkcorp.com or
 * by mail: 568 Ninth Street South 202 Naples, Florida 34102, the United States of America,
 * tel. +1 (239) 243 0206, fax +1 (239) 236-0738.
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'FaZend/View/Helper/Forma/Field.php';

/**
 * File uploading field
 *
 * @package helpers
 */
class FaZend_View_Helper_Forma_FieldFile extends FaZend_View_Helper_Forma_Field {

    /**
     * Get value from the form element
     *
     * @param Zend_Form_Element The element to work with
     * @return mixed
     **/
    public function deriveValue(Zend_Form_Element $element) {
        if ($element->isReceived())
            return $element->getFileName();
        return null;
    }

    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    protected function _getFormElement($name) {
        return new Zend_Form_Element_File($name);
    }

    /**
     * Configure form element
     *
     * @param Zend_Form_Element The element to configure
     * @return void
     */
    protected function _configureFormElement(Zend_Form_Element $element) {
        parent::_configureFormElement($element);
        $element
            ->setDecorators(array())
            ->addDecorator('File')
            ->addDecorator('HtmlTag', array('tag'=>'dd'))
            ->addDecorator('Label', array('escape' => false))
            ->setAttrib('class', 'file');
    }

}
