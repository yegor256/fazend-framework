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
 * Logging mechanism bootstraping
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 */
class FaZend_Application_Resource_fz_logger extends Zend_Application_Resource_ResourceAbstract
{

    /**
     * Initializes the resource
     *
     * @return void
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init()
    {
        $this->_bootstrap->bootstrap('fz_email');
        $this->_bootstrap->bootstrap('fz_errors');

        // remove all writers
        FaZend_Log::getInstance()->clean();

        // log errors in ALL environments
        FaZend_Log::getInstance()->addWriter('ErrorLog', 'ErrorLog');
        
        // if testing or development - log into memory as well
        if (APPLICATION_ENV !== 'production') {
            FaZend_Log::getInstance()->addWriter('Memory', 'FaZendDebug');
        }
    }

}