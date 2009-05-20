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

/**
 * User management controller
 *
 * You need to have these scripts (in /views/scripts/):
 *   remind.phtml
 *   reminded.phtml
 *   register.phtml
 *
 * You need to have these email templates (in /emails):
 *   RemindPassword.tmpl
 *   
 * You need these forms (in /config):
 *   RegisterAccount
 *   RemindPassword
 *
 */
class Fazend_UserController extends FaZend_Controller_Action {

        /**
         * Register new account
         *
         * @return void
         */
        public function registerAction() {

        	if (FaZend_User::isLoggedIn())
        		return $this->_forwardWithMessage('you are already logged in');

                $form = FaZend_Form::create('RegisterAccount', $this->view);
	        if (!$form->isFilled())
	        	return;

	        // try to register this new user
		try {
			$user = FaZend_User::register($form->email->getValue(), $form->password->getValue());
		} catch (FaZend_User_RegisterException $e) {
			$form->email->addError($e->getMessage());
			return;
		}	

		// login the found user
		$user->logIn();

		// go to the site index - should be improved - we should get back to the page where we were
		return $this->_redirect('index');

        }
        	
        /**
         * Remind password
         *
         * @return void
         */
        public function remindAction() {

        	if (FaZend_User::isLoggedIn()) 
        		return $this->_forwardWithMessage('you are already logged in');

                $form = FaZend_Form::create('RemindPassword', $this->view);
	        if (!$form->isFilled())
	        	return;

		try {
			$user = FaZend_User::findByEmail($form->email->getValue());
		} catch (FaZend_User_NotFoundException $e) {
			$form->email->addError('user not found');
			return;
		}

		// send password by email	
		FaZend_Email::create('RemindPassword.tmpl')
			->set('toEmail', $user->email)
			->set('toName', $user->nickname)
			->set('password', $user->password)
			->send();

		$this->_forward('reminded');	

        }
        	
        /**
         * Remind password - done
         *
         * @return void
         */
        public function remindedAction() {
        }

        /**
         * Log out
         *
         * @return void
         */
        public function logoutAction() {

        	if (!FaZend_User::isLoggedIn())
        		return $this->_forwardWithMessage('you are not logged in yet');

        	FaZend_User::getCurrentUser()->logOut();

        	return $this->_redirect('index');

        }

        /**
         * Log in
         *
         * @return void
         */
        public function loginAction() {

                $form = FaZend_Form::create('Login', $this->view);
	        if (!$form->isFilled())
	        	return;

        	try {
			$user = FaZend_User::findByEmail($form->email->getValue());

			if (!$user->isGoodPassword($form->pwd->getValue())) {
				$form->pwd->addError('incorrect password');
			} else {
				$user->logIn();
				$this->view->form = false;
			}

		} catch (FaZend_User_NotFoundException $e) {
			$form->email->addError('user not found');
		}

	}	

}
