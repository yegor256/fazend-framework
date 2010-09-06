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
 * @version $Id: Redirect.php 1747 2010-03-17 19:17:38Z yegor256@gmail.com $
 * @category FaZend
 */

require_once 'FaZend/View/Helper/Forma/Field.php';

/**
 * Redirect to the URL provided.
 *
 * @package helpers
 */
class FaZend_View_Helper_Forma_Behavior_RedirectUrl extends FaZend_View_Helper_Forma_Behavior_Abstract
{

    /**
     * Execute it
     *
     * @param string HTML to show (form or something else)
     * @param string Log of the form execution
     * @return void
     */
    public function run(&$html, $log)
    {
        // no parameter mean that we should redirect to the CURRENT page
        if (empty($this->_args)) {
            $request = Zend_Controller_Front::getInstance()->getRequest();
            $uri = substr($request->getRequestUri(), strlen($request->getBaseUrl()));
        } else {
            $uri = $this->_args[0];
        }
        
        Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
            ->gotoUrl($uri);
    }

}
