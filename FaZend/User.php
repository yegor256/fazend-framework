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
         * User is logged in?
         *
         * @return boolean
         */
	public static function isLoggedIn () {

		return Zend_Auth::getInstance()->hasIdentity();

	}

        /**
         * Returns current user
         *
         * @return Model_User
         */
	public static function getCurrentUser () {
		
		if (!self::isLoggedIn())
			throw new Exception ('user is not logged in');

		return new FaZend_User((int)Zend_Auth::getInstance()->getIdentity()->id);	
	}

        /**
         * Login user
         *
         * @return void
         */
	public static function logIn ($email, $password) {

		$authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
		$authAdapter->setTableName('user')
			->setIdentityColumn('email')
			->setCredentialColumn('password')
			->setIdentity(strtolower($email))
			->setCredential($password);

		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($authAdapter);

		if (!$result->isValid())
			throw new Model_User_LoginException(implode('; ', $result->getMessages()).' (code: #'.(-$result->getCode()).')');

		$data = $authAdapter->getResultRowObject(); 
		$auth->getStorage()->write($data);

	}

        /**
         * Logout
         *
         * @return void
         */
	public static function logOut () {

		Zend_Auth::getInstance()->clearIdentity();

	}

        /**
         * Register a new user
         *
         * @return boolean
         */
	public static function register ($email, $password) {

		$user = new FaZend_User();
		$user->email = strtolower($email);
		$user->password = $password;
		$user->save();

		return $user;	
	}

        /**
         * Get user by email
         *
         * @return boolean
         */
	public static function findByEmail ($email) {

		return self::retrieve()
			->where('email = ?', $email)
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

		return self::getCurrentUser()->id == $this->id;
	}
		
}
