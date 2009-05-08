<?php
/**
 *
 * Copyright (c) 2009, thePMP.com
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of thePMP.com. located at
 * www.thePMP.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@thePMP.com
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) thePMP.com, 2009
 * @version $Id$
 *
 */

/**
 * IndexController is the default controller for this application
 * 
 * Notice that we do not have to require 'Zend/Controller/Action.php', this
 * is because our application is using "autoloading" in the bootstrap.
 *
 * @see http://framework.zend.com/manual/en/zend.loader.html#zend.loader.load.autoload
 */
class FaZend_Controller_Squeeze extends FaZend_Controller_Action {

        /**
         *
         * @return void
         */
        public function indexAction() {

        	$file = FaZend_View_Helper_SqueezePNG::getImagePath();
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
