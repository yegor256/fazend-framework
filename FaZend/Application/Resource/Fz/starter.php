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
 * @version $Id: injector.php 1845 2010-04-09 14:01:44Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Initialize test starter
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 * @see FaZend_Test_Starter
 */
class FaZend_Application_Resource_fz_starter extends Zend_Application_Resource_ResourceAbstract
{
    
    /**
     * Starter object
     *
     * We make it static in order to protect against multiple
     * injections in multiple tests.
     *
     * @var FaZend_Test_Starter
     * @see init()
     */
    protected static $_starter = null;

    /**
     * Initializes the resource
     *
     * @return FaZend_Test_Injector|null
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init() 
    {
        if (!is_null(self::$_starter)) {
            return self::$_starter;
        } else {
            // something, to prevent multiple calls here
            self::$_starter = true;
        }

        // make sure that directory with test is includeable
        set_include_path(
            implode(
                PATH_SEPARATOR, 
                array(
                    realpath(APPLICATION_PATH . '/../../test'),
                    get_include_path(),
                )
            )
        );
        
        // we execute starter ONLY in CLI
        if (!defined('CLI_ENVIRONMENT')) {
            return false;
        }
            
        // we DON'T execute starter in production
        if (APPLICATION_ENV == 'production') {
            return false;
        }
    
        $starterPhp = APPLICATION_PATH . '/../../test/starter/Starter.php';
        if (!file_exists($starterPhp)) {
            return false;
        }

        eval('require_once $starterPhp;');
        self::$_starter = new Starter();
        self::$_starter->start();
        return self::$_starter;
    }
    
}
