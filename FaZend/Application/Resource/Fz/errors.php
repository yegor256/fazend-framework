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
     * Initializes the resource
     *
     * @return void
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init()
    {
        /**
         * @see Fazend_ErrorController
         */
        require_once FAZEND_APP_PATH . '/controllers/ErrorController.php';

        foreach ($this->getOptions() as $option=>$value) {
            switch ($option) {
                case 'isVisible':
                case 'display':
                    Fazend_ErrorController::setVisible($value);
                    break;
                default:
                    FaZend_Exception::raise(
                        'FaZend_Application_Resource_fz_errors_Exception', 
                        "Option '{$option}' is not valid in fz_errors"
                    );
            }
        }
    }
    
}
