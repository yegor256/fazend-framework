<?php
/**
 *
 * Copyright (c) 2009, FaZend.com
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of FaZend.com. located at
 * www.FaZend.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@FaZend.com
 *
 * @copyright Copyright (c) FaZend.com, 2009
 * @version $Id$
 *
 */

/**
 * Form
 *
 * @package FaZend 
 */
class FaZend_Form extends Zend_Form {

        /**
         * Create a new form and save it to View
         *
         * @return string
         */
	public static function create($file, Zend_View $view) {

        	$form = new FaZend_Form(new Zend_Config_Ini(APPLICATION_PATH . '/config/form'.$file.'.ini', 'form'));
        	$view->form = $form;

        	return $form;

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

		// validate all fields
		if (!$this->isValid($request->getPost() + $this->getValues()))
			return false;

		return true;
	}	

}
