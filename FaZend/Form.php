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
     * This method simplifies the process of form building. From .ini files.
     *
     * @param string File short name, will be translated to form<Name>.ini
     * @param ZendView Instance of view, $view->form will be set, if defined
     * @return FaZend_Form
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
     * @param Zend_Config Configuration of the form
     * @return string
     */
    public function setConfig(Zend_Config $config) {

        $this->addPrefixPath('FaZend_Form_Element', 'FaZend/Form/Element/', 'element');

        parent::setConfig($config);

        if (file_exists(APPLICATION_PATH . '/validators'))
            $this->addElementPrefixPath('Validator', APPLICATION_PATH . '/validators/', 'validate');

    }    

    /**
     * The form was filled properly?
     *
     * @return boolean
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
            if ($element->getLabel() == $request->getPost($element->getName())) {
                $submit = $element;
                break;
            }
        }

        // nothing clicked?
        if ($submit === false)
            return false;

        // validate all fields
        if (!$this->isValid($request->getPost() + $this->getValues()))
            return false;

        return true;
    }    

}
