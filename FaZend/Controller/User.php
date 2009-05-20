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
 *
 */
class FaZend_Controller_User extends FaZend_Controller_Action {

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

		$email = $form->email->getValue();	
		$password = $form->password->getValue();

		try {
			$user = FaZend_User::register($email, $password);
		} catch (FaZend_User_RegisterException $e) {
			$form->email->addError($e->getMessage());
			return;
		}	

		FaZend_User::logIn($email, $password);

		$this->_forward('index', 'index');

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
		} catch (Exception $e) {
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

        	FaZend_User::logOut();

        	$this->_forward('index', 'index');

        }

        /**
         * Log in
         *
         * @return void
         */
        public function loginAction() {

		$loginEmail = $this->getRequest()->getPost('loginEmail');
		$loginPassword = $this->getRequest()->getPost('loginPassword');

		if ($loginEmail && $loginPassword) {
			try {
				FaZend_User::logIn($loginEmail, $loginPassword);
			} catch (FaZend_User_LoginException $e) {
				$this->view->loginError = $e->getMessage();
			}	
		}	

		$this->view->loginEmail = $loginEmail;
		$this->view->loginPassword = $loginPassword;

		$this->_forward('index', 'index');
	}	

}
