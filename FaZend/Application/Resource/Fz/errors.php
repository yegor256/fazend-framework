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
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Errors management initialization
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_fz_errors extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Email of admin, who should receive errors
     *
     * @var string
     * @see setAdminEmail()
     */
    protected $_adminEmail = null;

    /**
     * Errors should be visible to users?
     *
     * @var boolean
     * @see setIsVisible()
     */
    protected $_isVisible = false;

    /**
     * Initializes the resource
     *
     * @return void
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init()
    {
        return $this;
    }
    
    /**
     * Shall we make errors visible to end user?
     *
     * @param boolean
     * @return $this
     */
    public function setIsVisible($isVisible) 
    {
        $this->_isVisible = $isVisible;
    }
    
    /**
     * Email of the admin
     *
     * @param string
     * @return $this
     */
    public function setAdminEmail($adminEmail) 
    {
        $this->_adminEmail = $adminEmail;
    }
    
    /**
     * Errors shall be visible to end user?
     *
     * @return boolean
     */
    public function getIsVisible() 
    {
        return $this->_isVisible;
    }
    
    /**
     * Email to send errors to
     *
     * @return string|null
     */
    public function getAdminEmail() 
    {
        return $this->_adminEmail;
    }
    
}
