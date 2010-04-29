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
 * @package controllers
 */
class Fazend_UserController extends FaZend_Controller_Action
{

    /**
     * Register new account
     *
     * @return void
     */
    public function registerAction()
    {
        if (FaZend_User::isLoggedIn()) {
            return $this->_redirectFlash('You are already logged in', 'notfound', 'error');
        }

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
        if (!$form->isFilled()) {
            return;
        }

        // try to register this new user
        $data = array();
        foreach ($form->getElements() as $element) {
            $name = $element->getName();

            if (!($element instanceof Zend_Form_Element_Text)) {
                continue;
            }

            $data[$name] = $element->getValue();
        }    

        try {
            $user = FaZend_User::register(
                $form->email->getValue(), 
                $form->password->getValue(), 
                $data
            );
        } catch (Zend_Db_Statement_Exception $e) {
            $form->email->addError("The user is already registered, try another email ({$e->getMessage()})");
            return;
        }    

        // login the found user
        $user->logIn();

        // kill the form
        $this->view->form = false;

        if (method_exists($this, 'registeredAction')) {
            $this->_helper->redirector->gotoSimple('registered');
        }
    }
        
    /**
     * Remind password
     *
     * @return void
     */
    public function remindAction()
    {
        if (FaZend_User::isLoggedIn()) {
            return $this->_redirectFlash('You are already logged in', 'notfound', 'error');
        }

        $form = FaZend_Form::create('RemindPassword', $this->view);
        if (!$form->isFilled()) {
            return;
        }

        try {
            $user = FaZend_User::findByEmail($form->email->getValue());
        } catch (FaZend_User_NotFoundException $e) {
            $form->email->addError('The user is not found');
            return;
        }

        // send password by email    
        FaZend_Email::create('RemindPassword.tmpl')
            ->set('toEmail', $user->email)
            ->set('toName', $user->email)
            ->set('password', $user->password)
            ->send();

        if (method_exists($this, 'remindedAction')) {
            $this->_helper->redirector->gotoSimple('reminded');
        }

        $this->_redirectFlash(_t('Password was sent by email'), 'remind');

    }
        
    /**
     * Log out current user and forward to the index/index
     *
     * @return void
     */
    public function logoutAction()
    {
        if (!FaZend_User::isLoggedIn()) {
            return $this->_redirectFlash(_t('You are not logged in yet'), 'notfound', 'error');
        }

        FaZend_User::logOut();

        // forward to the index action in index controller
        $this->_redirect('/');
    }

    /**
     * Log in
     *
     * @return void
     */
    public function loginAction()
    {
        $form = FaZend_Form::create('Login', $this->view);

        $pwdLabel = $form->pwd->getLabel();
        $remindUrl = $this->view->url(array('action'=>'remind'), 'user', true);

        $form->pwd->setLabel($pwdLabel . "&#32;(<a href='{$remindUrl}' title='remind password'>?</a>)");

        $form->pwd->getDecorator('label')->setOption('escape', false);
        if (!$form->isFilled()) {
            return;
        }

        try {
            $user = FaZend_User::findByEmail($form->email->getValue());

            if (!$user->isGoodPassword($form->pwd->getValue())) {
                $form->pwd->addError(_t('Incorrect password, try again'));
            } else {
                $user->logIn();
                $this->view->form = false;
                if (method_exists($this, 'loggedAction')) {
                    $this->_helper->redirector->gotoSimple('logged');
                }
            }
        } catch (FaZend_User_NotFoundException $e) {
            $form->email->addError('The user is not found');
        }

        if ($form->pwd->hasErrors() || $form->email->hasErrors()) {
            $form->pwd->setLabel(
                $pwdLabel . 
                "&#32;(<a href='{$remindUrl}' title='remind password by email?'>remind?</a>)"
            );
        }
    }    

}
