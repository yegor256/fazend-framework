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
        	$view->addHelperPath(FAZEND_PATH . '/View/Helper', 'FaZend_View_Helper');
        	$view->addFilterPath(FAZEND_PATH . '/View/Filter', 'FaZend_View_Filter');
        	$view->addFilter('HtmlCompressor');

                // Add it to the ViewRenderer
                $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
                $viewRenderer->setView($view);

                // view paginator
		Zend_Paginator::setDefaultScrollingStyle('Sliding');
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginationControl.phtml');

		// session
		if (defined('CLI_ENVIRONMENT'))
			Zend_Session::$_unitTestEnabled = true;

		// configure global routes for all
		$this->bootstrap('FrontController');
		$front = $this->getResource('FrontController');
		$router = new Zend_Controller_Router_Rewrite();
		$router->addConfig(new Zend_Config_Ini(FAZEND_PATH . '/Application/routes.ini', 'global'), 'routes');

		// configure custom routes
		if (file_exists(APPLICATION_PATH . '/config/routes.ini')) {
			$router->addConfig(new Zend_Config_Ini(APPLICATION_PATH . '/config/routes.ini', APPLICATION_ENV), 'routes');
		}
		$front->setRouter($router);

                // Return it, so that it can be stored by the bootstrap
                return $view;
	}


}

