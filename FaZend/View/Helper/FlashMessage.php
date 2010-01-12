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

require_once 'FaZend/View/Helper.php';

/**
 * Flash message to show in layout/scripts/layout.phtml
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_FlashMessage extends FaZend_View_Helper {

    /**
     * Render message
     *
     * @return void
     */
    public function flashMessage() {

        $actionHelperFlashMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
        $flashMessages = $actionHelperFlashMessenger->setNamespace('FaZend_Messages')->getMessages();

        if (empty($flashMessages)) {
            return;
        }

        return '<p class="flash">' . implode(";", $flashMessages) . '</p>';

    }

}
