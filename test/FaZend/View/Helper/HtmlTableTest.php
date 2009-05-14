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

class FaZend_View_Helper_HtmlTableTest extends AbstractTestCase {
	
	/**
	* Test table rendering
	*
	*/
	public function testHtmlTableWorks () {

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

		$array = array(
			array(
				'id' => 123,
				'email' => 'test@fazend.com',
				'password' => 'test'
			),
			array(
				'id' => 124,
				'email' => 'test@fazend.com',
				'password' => 'test'
			),
		);
		$paginator = Zend_Paginator::factory($array);
		$view->paginator = $paginator;

		$html = $view->render('table.phtml');

		$this->assertNotEquals(false, $html, "Empty HTML instead of table");
		$this->assertNotRegExp('/Exception/', $html);

	}

}
