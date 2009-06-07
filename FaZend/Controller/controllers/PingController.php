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
 * Foreign ping interface
 * 
 *
 */
class Fazend_PingController extends FaZend_Controller_Action {

        /**
         * We always reply with text/plain
         *
         * @return void
         */
        public function preDispatch() {

        	$this->getResponse()->setHeader('Content-type', 'text/plain');
	        $this->_helper->layout->disableLayout();
	        $this->view->setFilter(null);

	        // no limit in time execution
	        set_time_limit(0);

        }

        /**
         * Shows the list of all available actions
         *
         * @return void
         */
        public function indexAction() {


        }

        /**
         * Backup all live data and save them
         *
         * @return void
         */
        public function backupAction() {

        	$backup = new FaZend_Backup();
        	$backup->execute();
        	$this->view->log = $backup->getLog();

        }

}
