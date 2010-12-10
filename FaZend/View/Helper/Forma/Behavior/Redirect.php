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

require_once 'FaZend/View/Helper/Forma/Field.php';

/**
 * Redirect
 *
 * @package helpers
 */
class FaZend_View_Helper_Forma_Behavior_Redirect extends FaZend_View_Helper_Forma_Behavior_Abstract
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
            Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
                ->gotoUrl($uri);
            return;
        }

        // if callback provided, use it as URL
        $callback = $this->_args[0];
        if ($callback instanceof FaZend_Callback) {
            $args = $this->_methodArgs;
            $args[] = $this->_return;
            $path = call_user_func_array(
                array($callback, 'call'),
                $args
            );
            Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
                ->gotoUrl($path);
            return;
        }

        // redirect to the given address
        call_user_func_array(
            array(
                Zend_Controller_Action_HelperBroker::getStaticHelper('redirector'),
                'gotoSimple'
            ),
            $this->_args
        );
    }

}
