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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

require_once 'FaZend/View/Helper/Forma/Field.php';

/**
 * Add flash message
 *
 * @package helpers
 */
class FaZend_View_Helper_Forma_Behavior_Flash extends FaZend_View_Helper_Forma_Behavior_Abstract
{

    /**
     * Execute it
     *
     * @param string HTML to show (form or something else)
     * @param string Log of the form execution
     * @return void
     * @throws FaZend_View_Helper_Forma_Behavior_Flash_InvalidMnemo
     */
    public function run(&$html, $log)
    {
        foreach ($this->_args as &$arg) {
            if (!preg_match_all('/\{(\w+)\}/', $arg, $matches))
                continue;
            foreach ($matches[1] as $id=>$match) {
                if (!isset($this->_methodArgs[$match])) {
                    FaZend_Exception::raise(
                        'FaZend_View_Helper_Forma_Behavior_Flash_InvalidMnemo',
                        "Mnemo '{$match}' is not found in form"
                    );
                }
                $arg = str_replace(
                    $matches[0][$id], 
                    "\$this->_methodArgs['$match']", 
                    $arg
                );
                eval("\$arg = $arg;");
            }
        }
        
        $message = call_user_func_array('sprintf', $this->_args);
        Zend_Controller_Action_HelperBroker::getStaticHelper('flashMessenger')
            ->setNamespace('FaZend_Messages')->addMessage($message);
    }

}
