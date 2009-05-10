<?php
/**
 *
 * Copyright (c) 2009, Caybo.ru
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of Caybo.ru. located at
 * www.caybo.ru. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@caybo.ru
 *
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) Caybo.ru, 2009
 * @version $Id$
 *
 */

/**
* Bootstrap
*
* @package FaZend_Application
*/
class FaZend_Application_Bootstrap_Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

	/**
	* Initialize view
	*
	* @return
	*/
	protected function _initView() {

		// Initialize view
                $view = new Zend_View();

        	$view->addHelperPath(APPLICATION_PATH . '/helpers', 'Helper');
        	$view->addHelperPath(APPLICATION_PATH . '/../library/FaZend/View/Helper', 'FaZend_View_Helper');
        	$view->addFilterPath(APPLICATION_PATH . '/../library/FaZend/View/Filter', 'FaZend_View_Filter');
        	$view->addFilter('HtmlCompressor');

                // Add it to the ViewRenderer
                $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
                $viewRenderer->setView($view);

                // view paginator
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginationControl.phtml');

		// session
		if (defined('CLI_ENVIRONMENT')) {
			Zend_Session::$_unitTestEnabled = true;
		} else {
			if (isset(Zend_Registry::getInstance()->configuration->session->name))
				Zend_Session::start();
		}	

                // Return it, so that it can be stored by the bootstrap
                return $view;
	}


}

