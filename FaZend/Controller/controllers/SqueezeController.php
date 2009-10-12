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

require_once 'FaZend/Controller/Action.php';

/**
 * SqueezePNG delivery
 * 
 * @package controllers
 */
class Fazend_SqueezeController extends FaZend_Controller_Action {

    /**
     * Show the holder of all squeezed images
     *
     * @return void
     */
    public function indexAction() {

        $file = $this->view->squeezePNG()->getImagePath();
            if (!file_exists($file))
            return $this->_redirectFlash("file [{$file}] is not found");

        // return PNG as static (!) image    
        $this->_returnPNG(file_get_contents($file), false);

    }

}
