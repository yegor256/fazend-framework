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
 * Resolver from app.ini
 *
 *
 */
class FaZend_Auth_Adapter_Http_Resolver_Admins implements Zend_Auth_Adapter_Http_Resolver_Interface {

    protected $_scheme = 'basic';

    /**
     * Set scheme
     *
     * @return void
     */
    public function setScheme($scheme) {
        $this->_scheme = strtolower($scheme);
    }

    /**
     * Resolve it
     *
     * @return value|false
     */
    public function resolve($username, $realm) {

        $admins = FaZend_Properties::get()->admins->toArray();

        $username = str_replace('.', '_', $username);

        if (!isset($admins[$username]))
            return false;

        if ($this->_scheme == 'basic')
            return $admins[$username];
        else    
            return hash('md5', $admins[$username]);

    }
}
