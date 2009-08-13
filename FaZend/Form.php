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

require_once 'Zend/Form.php';

/**
 * Form, in a more convenient way to manage
 *
 * @package FaZend 
 */
class FaZend_Form extends Zend_Form {

    /**
     * Create a new form and save it to View
     *
     * @return string
     */
    public static function create($file, Zend_View $view = null) {

        $formIniFile = APPLICATION_PATH . '/config/form' . $file . '.ini';

        if (!file_exists($formIniFile))
            FaZend_Exception::raise('FaZend_Form_IniFileMissed', "File {$formIniFile} is missed");

        $form = new FaZend_Form(new Zend_Config_Ini($formIniFile, 'form'));

        // if it's null, ignore it
        if ($view !== null)
            $view->form = $form;

        return $form;

    }

    /**
     * Set config of the form from file
     *
     * We have to add here a path to FaZend decorators
     *
     * @return string
     */
    public function setConfig($config) {

        $this->addPrefixPath('FaZend_Form_Element', 'FaZend/Form/Element/', 'element');

        parent::setConfig($config);

        if (file_exists(APPLICATION_PATH . '/validators'))
            $this->addElementPrefixPath('Validator', APPLICATION_PATH . '/validators/', 'validate');

    }    

    /**
     * The form was filled properly?
     *
     * @return string
     */
    public function isFilled() {

        $request = Zend_Controller_Front::getInstance()->getRequest();

        // just show the form
        if (!$request->isPost())
            return false;

        // find SUBMIT element, and validate it
        $submit = false;
        foreach ($this->getElements() as $element) {
            if (!$element instanceof Zend_Form_Element_Submit)
                continue;

            // whether this particular form was submitted by this button?
            if ($element->getLabel() != $request->getPost($element->getName())) {
                $submit = $element;
                break;
            }
        }

        // if there is NO element with name 'submit', the form should
        // not be used in FaZend
        if (!$submit) {
            FaZend_Exception::raise('FaZend_Form_SubmitAbsentException',
                'Form does not have SUBMIT element');
        }

        // validate all fields
        if (!$this->isValid($request->getPost() + $this->getValues()))
            return false;

        return true;
    }    

}
