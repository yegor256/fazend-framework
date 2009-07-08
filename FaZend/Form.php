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

        $form = new FaZend_Form(new Zend_Config_Ini(APPLICATION_PATH . '/config/form' . $file . '.ini', 'form'));

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

        // whether this particular form was submitted    
        if ($this->submit->getLabel() != $request->getPost('submit'))    
            return false;

        // validate all fields
        if (!$this->isValid($request->getPost() + $this->getValues()))
            return false;

        return true;
    }    

}
