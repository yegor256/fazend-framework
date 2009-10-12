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
