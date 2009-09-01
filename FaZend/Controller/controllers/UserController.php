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

require_once 'FaZend/Controller/Action.php';

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
            return $this->_redirectFlash('already logged in');

        // if the RegisterAccount.ini file is missed
        // we should not raise the exception, but indicate about it
        // in the $form variable in View.
        try {
            $form = FaZend_Form::create('RegisterAccount', $this->view);
        } catch (FaZend_Form_IniFileMissed $e) {
            $this->view->form = $e->getMessage();
            return;
        }

        // if not yet filled - wait for it
        if (!$form->isFilled())
            return;

        // try to register this new user
        $data = array();
        foreach ($form->getElements() as $element) {
            $name = $element->getName();

            if (!($element instanceof Zend_Form_Element_Text))
                continue;

            $data[$name] = $element->getValue();
        }    

        try {
            
            $user = FaZend_User::register($form->email->getValue(), $form->password->getValue(), $data);

        } catch (Zend_Db_Statement_Exception $e) {

            $form->email->addError("user already registered, try another email ({$e->getMessage()})");
            return;

        }    

        // login the found user
        $user->logIn();

        // kill the form
        $this->view->form = false;

        if (method_exists($this, 'registeredAction'))
            $this->_helper->redirector->gotoSimple('registered');

    }
        
    /**
     * Remind password
     *
     * @return void
     */
    public function remindAction() {

        if (FaZend_User::isLoggedIn()) 
            return $this->_redirectFlash('already logged in');

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
            ->set('toName', $user->email)
            ->set('password', $user->password)
            ->send();

        $this->_redirectFlash('password sent by email', 'remind');    

    }
        
    /**
     * Log out current user and forward to the index/index
     *
     * @return void
     */
    public function logoutAction() {

        if (!FaZend_User::isLoggedIn())
            return $this->_redirectFlash('not logged in yet', 'index', 'index');

        FaZend_User::logOut();

        // forward to the index action in index controller
        $this->_redirect('/');

    }

    /**
     * Log in
     *
     * @return void
     */
    public function loginAction() {

        $form = FaZend_Form::create('Login', $this->view);

        $pwdLabel = $form->pwd->getLabel();
        $remindUrl = $this->view->url(array('action'=>'remind'), 'user', true);

        $form->pwd->setLabel($pwdLabel . "&#32;(<a href='{$remindUrl}'>?</a>)");

        $form->pwd->getDecorator('label')->setOption('escape', false);
        if (!$form->isFilled())
            return;

        try {

            $user = FaZend_User::findByEmail($form->email->getValue());

            if (!$user->isGoodPassword($form->pwd->getValue())) {
                $form->pwd->addError('incorrect password');
            } else {
                $user->logIn();
                $this->view->form = false;
                if (method_exists($this, 'loggedAction'))
                    $this->_helper->redirector->gotoSimple('logged');
            }

        } catch (FaZend_User_NotFoundException $e) {

            $form->email->addError('user not found');

        }

        if ($form->pwd->hasErrors() || $form->email->hasErrors()) {
            $form->pwd->setLabel($pwdLabel . "&#32;(<a href='{$remindUrl}'>remind password</a>)");
        }

    }    

}
