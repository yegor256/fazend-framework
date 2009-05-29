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
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 */
class FaZend_View_Helper_GoogleAnalytics {

	/**
	 * Instance of the view
	 *
	 * @var Zend_View
	 */
	private $_view;

	/**
	* Save view locally
	*
	* @return void
	*/
	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}           

	/**
	* Get view saved locally
	*
	* @return Zend_View
	*/
	public function getView() {
		return $this->_view;
	}

	/**
	* Show GA script
	*
	* @return Zend_View
	*/
	public function googleAnalytics() {

		if (APPLICATION_ENV != 'production')
			return "<!-- google analytics skipped -->\n";

		$this->getView()->addScriptPath(FAZEND_PATH . '/View/scripts/');
		return $this->getView()->render('google-analytics.phtml');

	}	

}
