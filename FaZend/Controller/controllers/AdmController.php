<?php
/**
 * FaZend Framework
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt. It is also available 
 * through the world-wide-web at this URL: http://www.fazend.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@fazend.com so we can send you a copy immediately.
 *
 * @copyright Copyright (c) FaZend.com
 * @version $Id$
 * @category FaZend
 */

require_once 'FaZend/Controller/Action.php';

/**
 * Admin controller
 *
 * @package controllers
 */
class Fazend_AdmController extends FaZend_Controller_Panel
{

    /**
     * Get action name
     *
     * @return void
     */
    public function preDispatch() 
    {
        $this->view->action = $this->getRequest()->getActionName();    
        parent::preDispatch();
    }
        
    /**
     * Show db schema
     *
     * @return void
     */
    public function schemaAction() 
    {
        try {
            $adapter = Zend_Db_Table::getDefaultAdapter();
        } catch (Exception $e) {
            $this->view->schema = 'There is no database in the project';
            return;
        }

        $tables = $adapter->listTables();

        $sql = '';
        foreach ($tables as $table) {

            try {
                $row = $adapter->fetchRow("show create table {$table}");

                if (isset($row['Create Table']))
                    $sql .= $row['Create Table'];
                elseif (isset($row['Create View']))
                    $sql .= $row['Create View'];
                else
                    $sql .= "error in {$table}";

            } catch (Exception $e) {
                $sql .= "Failed to retrieve information about '{$table}': " . $e->getMessage();
            }

            $sql .= "\n\n";    
        }    

        $this->view->schema = $sql;
    }

    /**
     * Show server error log file
     *
     * @return void
     */
    public function logAction() 
    {
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
    public function tablesAction() 
    {
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
    public function squeezeAction() 
    {
        if ($this->_hasParam('reload'))
            $this->view->squeezePNG()->startOver();
    }

    /**
     * Show backup status
     *
     * @return void
     */
    public function backupAction() 
    {
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

    /**
     * Show POS content
     *
     * @return void
     */
    public function posAction() {
        if (!FaZend_Pos_Root::exists())
            return $this->_redirectFlash('POS does not exist', 'index');
            
        if ($this->_hasParam('object'))
            $this->view->node = FaZend_Pos_Abstract::root()->ps()->findById($this->_getParam('object'));
    }
    
}
            