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
 * Replace form with new HTML
 *
 * @package helpers
 */
class FaZend_View_Helper_Forma_Behavior_Replace extends FaZend_View_Helper_Forma_Behavior_Abstract
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
        // build HTML
        $html = call_user_func_array(
            array(
                FaZend_Callback::factory($this->_args[0]),
                'call'
            ),
            $this->_methodArgs
        );
    }

}
