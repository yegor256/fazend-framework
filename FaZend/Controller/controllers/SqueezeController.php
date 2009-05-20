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

/**
 *
 * 
 *
 */
class Fazend_SqueezeController extends FaZend_Controller_Action {

        /**
         *
         * @return void
         */
        public function indexAction() {

        	$file = $this->view->squeezePNG()->getImagePath();
            	if (!file_exists($file))
        		return $this->_forwardWithMessage('file '.$file.' is not found');

        	// when it was created	
        	header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($file)) . ' GMT');

        	// in 30 days to reload!
        	header('Expires: '.gmdate('D, d M Y H:i:s', filemtime($file) + 60 * 60 * 24 * 30) . ' GMT');

        	// tell the browser NOT to reload the image
        	header('Cache-Control: public;');
        	header('Pragma:;');

        	$this->_returnPNG(file_get_contents($file));

        }

}
