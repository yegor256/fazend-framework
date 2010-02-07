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
 * One simple user
 *
 * It is assumed that there is a Table in the DB: user (id, email, password)
 *
 * @package User
 */
class FaZend_User extends FaZend_Db_Table_ActiveRow_user
{

    /**
     * Auth, to be retrieved by self::_auth()
     *
     * @var Zend_Auth
     * @see _auth()
     */
    private static $_auth = null;

    /**
     * Login status, to be retrieved by self::isLoggedIn()
     *
     * It either contains an instance of FaZend_User, which is a 
     * currently logged in user. Or it is set to FALSE, which means
     * that nobody is logged in now. NULL means that the status is
     * not yet checked.
     *
     * @var null|false|FaZend_User
     * @see isLoggedIn()
     */
    private static $_loggedIn = null;
    
    /**
     * Name of the class to use
     *
     * If you change this variable by means of setRowClass(), all
     * instances of users will be in this class. It's normal to
     * change this name in your bootstrap.php
     *
     * @var string Name of the class to instantiate
     * @see setRowClass()
     */
    protected static $_rowClass = 'FaZend_User';

    /**
     * Set class name to use in all static methods
     *
     * @param string Name of the class
     * @return void
     * @see self::$_rowClass
     **/
    public static function setRowClass($className) 
    {
        self::$_rowClass = $className;
    }

    /**
     * User is logged in?
     *
     * This method will check the status inside the class first (maybe
     * someone was logged in already during the execution of this script). 
     * If the status is not set (self::$_loggedIn is NULL) that we will
     * try to get information about the user from session, by means of
     * _auth(). If someone is found there - we will instantiate a user class,
     * login it and save for future use.
     *
     * @return boolean
     * @see self::$_loggedIn
     * @see _auth()
     */
    public static function isLoggedIn() 
    {
        // If the status is already set to an instance of class
        // or to FALSE -- we return the boolean
        if (!is_null(self::$_loggedIn))
            return (bool)self::$_loggedIn;

        // try to analyze the situation in session
        if (self::_auth()->hasIdentity()) {
            $class = self::$_rowClass;
            $user = new $class(intval(self::_auth()->getIdentity()->id));
            if ($user->exists() && ($user->password == self::_auth()->getIdentity()->password)) {
                $user->logIn();
                return true;
            }
        }
        
        return self::$_loggedIn = false;
    }

    /**
     * Returns current user, if he is logged in
     *
     * @return FaZend_User The user who is currently logged in
     * @throws FaZend_User_NotLoggedIn If there is no logged in user
     * @see isLoggedIn()
     */
    public static function getCurrentUser() 
    {
        if (!self::isLoggedIn()) {
            FaZend_Exception::raise(
                'FaZend_User_NotLoggedIn', 
                'User is not logged in yet'
            );
        }
        return self::$_loggedIn;
    }

    /**
     * Login this user
     *
     * @return void
     * @throws FaZend_User_LoginFailed
     * @see _auth()
     */
    public function logIn() 
    {
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('user')
            ->setIdentityColumn('email')
            ->setCredentialColumn('password')
            ->setIdentity(strtolower($this->email))
            ->setCredential($this->password);

        $result = self::_auth()->authenticate($authAdapter);

        // if we failed to login the user
        if (!$result->isValid()) {
            FaZend_Exception::raise(
                'FaZend_User_LoginFailed', 
                implode('; ', $result->getMessages()) . ' (code: #' . (-$result->getCode()) . ')'
            );
        }

        // save information about the user into session
        self::_auth()->getStorage()->write($authAdapter->getResultRowObject());

        // forget previous status
        self::$_loggedIn = $this;

        // remember me as a logged in user
        Zend_Session::rememberMe();
    }

    /**
     * Logout current user
     *
     * @return void
     * @see self::$_loggedIn
     * @see _auth()
     */
    public static function logOut() 
    {
        // forget previous status
        self::$_loggedIn = false;

        // clean session
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
    public static function register($email, $password, array $data = array()) 
    {
        $className = self::$_rowClass;
        $user = new $className();
        
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
    public static function findByEmail($email) 
    {
        return self::retrieve()
            ->where('email = ?', $email)
            ->setRowClass(self::$_rowClass)
            ->fetchRow();
    }

    /**
     * This user is current user logged in?
     *
     * @return boolean
     */
    public function isCurrentUser() 
    {
        if (!self::isLoggedIn())
            return false;
        return strval(self::getCurrentUser()) === strval($this);
    }
        
    /**
     * This password is ok for the user?
     *
     * @param string Password
     * @return boolean
     */
    public function isGoodPassword($password) 
    {
        return $this->password == $password;
    }

    /**
     * Get auth from Zend_Auth
     *
     * @return Zend_Auth
     */
    protected static function _auth() 
    {
        if (is_null(self::$_auth))
            self::$_auth = Zend_Auth::getInstance();
        return self::$_auth;
    }

}
