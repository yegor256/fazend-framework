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
 * Admin controller
 *
 *
 */
class Fazend_AdmController extends FaZend_Controller_Action {

        /**
         * Block access
         *
         * @see http://framework.zend.com/manual/en/zend.auth.adapter.http.html
         * @return void
         */
        public function preDispatch() {

        	if (APPLICATION_ENV == 'testing')
        		return;

        	$request = $this->getRequest();
		$response = $this->getResponse();

		$adapter = new Zend_Auth_Adapter_Http(array(
			'accept_schemes' => 'basic',
			'realm' => 'adm',
			'digest_domains' => '/adm',
			'nonce_timeout' => 3600));

		$resolver = new Zend_Auth_Adapter_Http_Resolver_File();
		$resolver->setFile(APPLICATION_PATH . '/config/admins.txt');	
		$adapter->setBasicResolver($resolver);

		$adapter->setRequest($request);
		$adapter->setResponse($response);

		if (!$adapter->authenticate()->isValid())
			return $this->_forwardWithMessage('try again');

        }
        	
        /**
         * Front page
         *
         * @return void
         */
        public function indexAction() {

        }
        	
        /**
         * Show db schema
         *
         * @return void
         */
        public function schemaAction() {

        	$adapter = Zend_Db_Table::getDefaultAdapter();

        	$tables = $adapter->listTables();

        	$sql = '';
        	foreach ($tables as $table) {
		      	$row = $adapter->query("show create table {$table}")->toArray();
		      	$sql .= $row['1'];
		}	

        	$this->view->schema = $sql;

        }

}
                	