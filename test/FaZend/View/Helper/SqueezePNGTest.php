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

require_once 'AbstractTestCase.php';

class FaZend_View_Helper_SqueezePNGTest extends AbstractTestCase {
	
	/**
	* Test table rendering
	*
	*/
	public function testSqueezePNGWorks () {

		//$view = new Zend_View();

		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer')->view;
		$view->setScriptPath(APPLICATION_PATH . '/views/scripts');

		$route = new Zend_Controller_Router_Route(
			'abc',
			array(
				'controller' => 'index',
				'action'     => 'index'
			)
		);

		Zend_Controller_Front::getInstance()->getRouter()->addRoute('default', $route);

		$html = $view->render('squeeze.phtml');

		$this->assertNotEquals(false, $html, "Empty HTML instead of images");

	}

}
