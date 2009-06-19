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
 * Simplified interface for exception throwing
 *
 * @package Model
 */
class FaZend_Exception extends Exception {

        /**
         * Creates exception class on fly
         *
         * <code>
         * if (.. something wrong ..) {
         *   throw Zend_Exception::create('Model_User_BadUser', 'Your user Id is incorrect');
         * }
         * </code>
         *
         * @param string Exception class name, will be created or loaded
         * @param string Message to be sent inside this class
         * @return boolean
         */
	public static function raise($class, $message = false, $parent = 'Exception') {

        	if (!class_exists($class, false)) {
			// try to load if, maybe it exists
			$autoloader = Zend_Loader_Autoloader::getInstance();
			$autoloader->suppressNotFoundWarnings(true);

			// no, it doesn't exist - so we create it!
			if (!@$autoloader->autoload($class)) {
        			eval("class {$class} extends {$parent} {};");	
        		}	
		}	

		throw new $class($message);

	}

}
