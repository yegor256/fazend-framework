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

        $this->view->action = $this->getRequest()->getActionName();    

        if (APPLICATION_ENV == 'testing')
            return;

        $resolver = new FaZend_Auth_Adapter_Http_Resolver_Admins();
        $resolver->setScheme('basic');    

        // all this will work ONLY if PHP is installed as Apache Module
        // @see: http://www.php.net/features.http-auth
        if (FaZend_User::isLoggedIn() && $resolver->resolve(FaZend_User::getCurrentUser()->email, 'adm'))
            return;

        $adapter = new Zend_Auth_Adapter_Http(array(
            'accept_schemes' => 'basic',
            'realm' => 'adm'));

        $adapter->setBasicResolver($resolver);
        $adapter->setRequest($this->getRequest());
        $adapter->setResponse($this->getResponse());

        $result = $adapter->authenticate();
        if (!$result->isValid()) {
            return $this->_forwardWithMessage('authorization failed (code #' . abs($result->getCode()). '): ' . 
                implode('; ', $result->getMessages()));
        }    

    }
        
    /**
     * 
     *
     * @return void
     */
    public function postDispatch() {
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

                  $row = $adapter->fetchRow("show create table {$table}");

                  if (isset($row['Create Table']))
                      $sql .= $row['Create Table'];
            elseif (isset($row['Create View']))
                      $sql .= $row['Create View'];
            else
                $sql .= "error in {$table}";

            $sql .= "\n\n";    

        }    

        $this->view->schema = $sql;

    }

    /**
     * Show server error log file
     *
     * @return void
     */
    public function logAction() {

        $this->view->filePath = ini_get('error_log');

        // maybe error_log is not set?
        if ($this->view->filePath) {

            if ($this->_hasParam('clear'))
                file_put_contents($this->view->filePath, 'cleared on ' . date('m/d/y h:i:s'));

            $this->view->log = file_get_contents($this->view->filePath);

        } else
            $this->view->log = 'no [phpSettings.error_log] variable set in app.ini';

    }

    /**
     * Show content of tables
     *
     * @return void
     */
    public function tablesAction() {

        $adapter = Zend_Db_Table::getDefaultAdapter();
        $this->view->tables = array_diff($adapter->listTables(), array('changelog'));

        if (!$this->_hasParam('table'))
            return;

        $this->view->table = $table = $this->_getParam('table');

        eval ("\$retrieve = FaZend_Db_ActiveTable_{$table}::retrieve(); \$iterator = \$retrieve->fetchAll();");

        $this->view->retrieve = $retrieve;

        FaZend_Paginator::addPaginator($iterator, $this->view, $this->_getParamOrFalse('page'));

    }

    /**
     * Show content of tables
     *
     * @return void
     */
    public function squeezeAction() {

        if ($this->_hasParam('reload'))
            $this->view->squeezePNG()->startOver();

    }

    /**
     * Show backup status
     *
     * @return void
     */
    public function backupAction() {

        $this->view->backup = new FaZend_Backup();

        if ($this->_hasParam('clear'))
            $this->view->backup->clearSemaphore();

        $files = array();    
        foreach ($this->view->backup->getS3Files() as $object) {

            $info = $this->view->backup->getS3FileInfo($object);
            $info['name'] = $object;

            $files[] = $info;
        }

        usort($files, create_function('$a, $b', 'return $a["mtime"] > $b["mtime"];'));

        $this->view->files = $files;

    }

}
            