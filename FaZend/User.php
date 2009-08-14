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
 * One simple user
 *
 * It is assumed that there is a Table in the DB: user (id, email, password)
 *
 * @package Model
 */
class FaZend_User extends FaZend_Db_Table_ActiveRow_user {

    /**
     * Auth
     *
     * @var Zend_Auth
     */
    private static $_auth;

    /**
     * Login status
     *
     * @var boolean
     */
    private static $_loggedIn;

    /**
     * User is logged in?
     *
     * @return boolean
     */
    public static function isLoggedIn () {

        if (isset(self::$_loggedIn))
            return self::$_loggedIn;

        // try to analyze the situation in session
        $loggedIn = false;
        if (self::_auth()->hasIdentity()) {
            try {
                $user = FaZend_User::findByEmail(self::_auth()->getIdentity()->email);
                // sanity check
                if ($user->password != self::_auth()->getIdentity()->password)
                    FaZend_Exception::raise('FaZend_User_InvalidPassword');
 
                // yes, we're here!
                $loggedIn = true;
            } catch (FaZend_User_NotFoundException $e) {
            } catch (FaZend_User_InvalidPassword $e) {
            }
        }
        return self::$_loggedIn = $loggedIn;
    }

    /**
     * Returns current user
     *
     * @return FaZend_User
     * @throw FaZend_User_NotLoggedIn
     */
    public static function getCurrentUser () {
        
        if (!self::isLoggedIn())
            FaZend_Exception::raise('FaZend_User_NotLoggedIn', 'user is not logged in');

        return FaZend_User::findByEmail(self::_auth()->getIdentity()->email);    
    }

    /**
     * Login this user
     *
     * @return void
     * @throw FaZend_User_LoginFailed
     */
    public function logIn () {

        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('user')
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setIdentity(strtolower($this->email))
            ->setCredential($this->password);

        $result = self::_auth()->authenticate($authAdapter);

        if (!$result->isValid())
            FaZend_Exception::raise('FaZend_User_LoginFailed', implode('; ', $result->getMessages()).' (code: #'.(-$result->getCode()).')');

        $data = $authAdapter->getResultRowObject(); 
        self::_auth()->getStorage()->write($data);

        // forget previous status
        self::$_loggedIn = true;

    }

    /**
     * Logout current user
     *
     * @return void
     */
    public function logOut () {

        // forget previous status
        self::$_loggedIn = false;

        self::_auth()->clearIdentity();

    }

    /**
     * Register a new user
     *
     * It is assumed that you have a table in DB with these fields:
     *
     * <code>
     * CREATE TABLE IF NOT EXISTS `user` (
     * `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT "Unique ID of the user",
     * `email` VARBINARY(200) NOT NULL COMMENT "Unique user email",
     * `password` VARBINARY(50) NOT NULL COMMENT "User password",
     * `nickname` VARCHAR(50) NOT NULL COMMENT "User nickname publicly available",
     * `photo` MEDIUMBLOB COMMENT "Photo in BLOB form (GIF, JPG, PNG), 150x150",
     * `text` TEXT COMMENT "User profile",
     * PRIMARY KEY USING BTREE (`id`),
     * UNIQUE (`email`)
     * ) AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ENGINE=InnoDB;
     * </code>
     *
     * Columns nickname, photo, and text are optional. Id, email and password are mandatory
     *
     * @param string Email of the user
     * @param string Password
     * @param array Associative array of other data for USER table
     * @return boolean
     */
    public static function register ($email, $password, array $data = array()) {

        $user = new FaZend_User();
        $user->email = strtolower($email);
        $user->password = $password;

        foreach ($data as $key=>$value)
            $user->$key = $value;

        $user->save();

        // we're trying to send an email to admin about this
        // account registration. if this file (emails/adminNewUserRegistered.tmpl) exists
        // the email will be sent. otherwise the exception will come from FaZend_Email
        // and we skip this process
        try {

            // send email to admin, with one variable inside
            // the template should use it like $this->user
            FaZend_Email::create('adminNewUserRegistered.tmpl')
                ->set('user', $user)
                ->send();

        } catch (FaZend_Email_NoTemplate $e) {
            // don't do anything        
        }

        // now we will try send an email to the user, telling him/her
        // about successful registration of the account
        try {

            // send email to admin, with one variable inside
            // the template should use it like $this->user
            FaZend_Email::create('AccountRegistered.tmpl')
                ->set('toEmail', $user->email)
                ->set('toName', $user->email)
                ->set('user', $user)
                ->send();

        } catch (FaZend_Email_NoTemplate $e) {
            // don't do anything        
        }

        return $user;    

    }

    /**
     * Get user by email
     *
     * @param string Email of the user
     * @return boolean
     */
    public static function findByEmail ($email) {

        return self::retrieve()
            ->where('email = ?', $email)
            ->setRowClass('FaZend_User')
            ->fetchRow();

    }

    /**
     * This user is current user logged in?
     *
     * @return boolean
     */
    public function isCurrentUser () {
        if (!self::isLoggedIn())
            return false;

        return self::getCurrentUser()->__id == $this->__id;
    }
        
    /**
     * This password is ok for the user?
     *
     * @param string Password
     * @return boolean
     */
    public function isGoodPassword ($password) {
        return $this->password == $password;
    }

    /**
     * Get auth
     *
     * @return Zend_Auth
     */
    protected static function _auth() {

        if (!isset(self::$_auth)) {
            self::$_auth = Zend_Auth::getInstance();
        }

        return self::$_auth;

    }

}
