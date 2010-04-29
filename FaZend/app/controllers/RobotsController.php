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

require_once 'FaZend/Controller/Action.php';

/**
 * Robots.txt
 * 
 * @package controllers
 */
class Fazend_RobotsController extends FaZend_Controller_Action
{

    /**
     * Show the file
     *
     * @return void
     */
    public function indexAction()
    {
        $this->getResponse()->setHeader('Content-type', 'text/plain');
        $this->_helper->layout->disableLayout();
        $this->view->setFilter(null);
    }

}
