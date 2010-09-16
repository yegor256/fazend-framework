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
 * @see FaZend_Controller_Action
 */
require_once 'FaZend/Controller/Action.php';

/**
 * SqueezePNG delivery
 * 
 * @package controllers
 */
class Fazend_SqueezeController extends FaZend_Controller_Action
{

    /**
     * Show the holder of all squeezed images
     *
     * @return void
     */
    public function indexAction()
    {
        $file = $this->view->squeezePNG()->getImagePath();
        if (!file_exists($file)) {
            return $this->_redirectFlash("file [{$file}] is not found");
        }
        // return PNG as static (!) image    
        $this->_returnPNG(file_get_contents($file), false);
    }

}
