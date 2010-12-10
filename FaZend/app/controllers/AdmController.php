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

/**
 * Admin controller
 *
 * @package controllers
 */
class Fazend_AdmController extends FaZend_Controller_Action
{

    /**
     * Session cache
     */
    protected $_session;

    /**
     * Get action name
     *
     * @return void
     */
    public function preDispatch()
    {
        // no login in testing/development environment
        if ((APPLICATION_ENV === 'production') && !self::_isLoggedIn()) {
            return $this->_forward('login');
        }

        $this->view->action = $this->getRequest()->getActionName();
        parent::preDispatch();
    }

    /**
     * Show content of tables
     *
     * @return void
     */
    public function squeezeAction()
    {
        if ($this->_hasParam('reload')) {
            $this->view->squeezePNG()->startOver();
        }
    }

    /**
     * Login
     *
     * @return void
     */
    public function loginAction()
    {
        $form = $this->view->form = new FaZend_Form();

        $email = new Zend_Form_Element_Text('email');
        $email->setLabel('Email:')
            ->setRequired();
        $form->addElement($email);

        $pwd = new Zend_Form_Element_Password('password');
        $pwd->setLabel('Password:')
            ->setRequired();
        $form->addElement($pwd);

        $form->addElement('submit', 'Login');

        if (!$form->isFilled())
            return;

        try {
            $this->_logIn($email->getValue(), $pwd->getValue());
        } catch (LoginException $e) {
            $email->addError($e->getMessage());
            return;
        }
        return $this->_forward('index');
    }

    /**
     * Restrict access to certain areas
     *
     * @return void
     */
    public function restrictAction()
    {
    }

    /**
     * The admin is logged in?
     *
     * @return boolean
     */
    protected function _isLoggedIn()
    {
        return !empty($this->_session()->user);
    }

    /**
     * Log in the admin
     *
     * If login matches with password - will login. Otherwise won't do anything.
     *
     * @param string Email to log in
     * @param string Password
     * @return boolean
     * @throws LoginException If fails
     */
    protected function _logIn($email, $password)
    {
        $accessFile = APPLICATION_PATH . '/deploy/access.txt';
        if (!@file_exists($accessFile)) {
            FaZend_Exception::raise(
                'LoginException',
                "Access control file is absent, refer to admin"
            );
        }

        $count = 0;
        $lines = @file($accessFile);
        foreach ($lines as $line) {
            $matches = array();
            if (!preg_match('/^([@\.\-\w\d]+):([\w\d]+)$/', $line, $matches)) {
                continue;
            }

            // calculate the number of users in the file
            $count++;

            // if this is not the required email (another user)
            if ($matches[1] != $email) {
                continue;
            }

            // wrong password?
            if ($matches[2] != md5($password)) {
                FaZend_Exception::raise(
                    'LoginException',
                    "Wrong password (" . str_repeat('*', strlen($password)) . "), try again"
                );
            }

            // everything is fine, we should log him in
            $this->_session()->user = $email;
            return true;
        }

        FaZend_Exception::raise(
            'LoginException',
            "The email is not found in ACL ($count)"
        );
    }

    /**
     * Get session
     *
     * @return Zend_Session_Namespace
     */
    protected function _session()
    {
        if (!isset($this->_session)) {
            $this->_session = new Zend_Session_Namespace('fz');
        }
        return $this->_session;
    }

}
