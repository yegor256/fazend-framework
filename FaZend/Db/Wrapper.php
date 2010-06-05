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

/**
 * Wrapper around tables, adapters etc. You should NEVER instantiate it directly!
 *
 * This class is created by FaZend_Db_Table_ActiveRow::retrieve().
 *
 * Usase sample:
 *
 * <code>
 * $rowset = FaZend_Db_ActiveTable_user::retrieve()
 *    ->where('email = ?', 'me@example.com')
 *    ->fetchAll();
 * </code>
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 * @see FaZend_Db_Table_ActiveRow::retrieve()
 * @package Db
 */
class FaZend_Db_Wrapper
{

    /**
     * Table
     *
     * @var Zend_Db_Table|string
     */
    private $_table;

    /**
     * Select
     *
     * @var Zend_Db_Select
     */
    private $_select;

    /**
     * Keep silence and return FALSE if fetchRow doesn't find anything
     *
     * Normal behavior of fetchRow() is to TRY to fetch a row and 
     * throw an exception if nothing found in the DB. When $_silenceIfEmpty
     * is set there won't be any exception, but FALSE will be returned.
     *
     * @var boolean
     * @see setSilenceIfEmpty()
     * @see fetchRow()
     */
    private $_silenceIfEmpty = false;

    /**
     * Set FROM attribute to select?
     *
     * @var boolean
     */
    private $_setFrom = true;

    /**
     * Create a select object
     *
     * @param string Name of the DB table
     * @param boolean Set FROM attribute to the select or not
     * @return void
     */
    public function __construct($table, $setFrom = true) 
    {
        $this->_table = $table;
        $this->_setFrom = $setFrom;
    }

    /**
     * Set row class
     *
     * @param string Name of the class
     * @return $this
     */
    public function setRowClass($rowClass) 
    {
        $this->table()->setRowClass($rowClass);
        return $this;
    }

    /**
     * Get row class name
     *
     * @return string
     */
    public function getRowClass()
    {
        return $this->table()->getRowClass();
    }

    /**
     * Keep silence and return FALSE if fetchRow() doesn't find anything
     *
     * @param boolean Set it (TRUE) or reset (FALSE)
     * @return $this
     */
    public function setSilenceIfEmpty($flag = true)
    {
        $this->_silenceIfEmpty = $flag;
        return $this;
    }

    /**
     * Keep silence and return FALSE if fetchRow() doesn't find anything
     *
     * @return boolean
     */
    public function getSilenceIfEmpty()
    {
        return $this->_silenceIfEmpty;
    }

    /**
     * Return the table
     *
     * @return Zend_Db_Table
     */
    public function table() 
    {
        // if we just initialized the class with constructor
        if (is_string($this->_table)) {
            $this->_table = FaZend_Db_ActiveTable::createTableClass($this->_table);
        }    
        return $this->_table;
    }

    /**
     * Return the SELECT object (on-fly)
     *
     * @param array|null Bindings in array, if they are required in query
     * @return Zend_Db_Table_Select
     */
    public function select(array $bind = null) 
    {
        if (!isset($this->_select)) {
            $this->_select = $this->table()->select();
            $this->_select->setIntegrityCheck(false);
            if ($this->_setFrom) {
                $this->_select->from($this->table()->info(Zend_Db_Table_Abstract::NAME));
            }
        }

        // add bindings to the query, if necessary
        if (!is_null($bind)) {
            $this->_select->bind($bind);
        }
        return $this->_select;
    }

    /**
     * Fetch one row from the table
     *
     * @param array|null Bindings in array, if they are required in query
     * @return Zend_Db_Table_Row
     * @throws FaZend_Db_Table_NotFoundException
     */
    public function fetchRow(array $bind = null) 
    {
        // get row with fetchRow from the select we have
        $row = $this->table()->fetchRow(
            $this->select($bind)
        );

        // if we should keep silence - just return what we got
        if ($this->getSilenceIfEmpty() && !$row) {
            return false;
        }

        // if the result is OK - just return it
        if ($row) {
            return $row;
        }

        // we should create this class in any case - no matter whether
        // we throw the exception or not. because the try{}catch block
        // will expect this class and will fail to load it    
        if (strpos($this->getRowClass(), 'FaZend_Db_Table_ActiveRow') === 0) {
            $exceptionClassName = 'Exception';
        } else {
            $exceptionClassName = $this->getRowClass() . '_NotFoundException';
        }

        // raise this exception
        FaZend_Exception::raise(
            $exceptionClassName, 
            sprintf('Row not found in %s with "%s"', $this->getRowClass(), $this->select()),
            'FaZend_Db_Table_NotFoundException'
        );
    }

    /**
     * Fetch wrapper
     *
     * Lazy loading is implemented here. We do NOT execute a query when
     * you call fetchAll(), we just create a wrapper for such a call and
     * return its instance. When you actually start using the result of
     * the query - we execute the call to DB.
     *
     * @param array|null Bindings in array, if they are required in query
     * @return FaZend_Db_RowsetWrapper
     * @uses FaZend_Db_RowsetWrapper
     */
    public function fetchAll(array $bind = null) 
    {
        return new FaZend_Db_RowsetWrapper(
            $this->select($bind),
            $this->table()
        );
    }

    /**
     * Fetch pairs
     *
     * @param array|null Bindings in array, if they are required in query
     * @return array
     */
    public function fetchPairs(array $bind = null) 
    {
        return $this->table()->getAdapter()->fetchPairs(
            $this->select($bind)
        );
    }

    /**
     * Fetch one cell
     *
     * @param array|null Bindings in array, if they are required in query
     * @return string
     */
    public function fetchOne(array $bind = null) 
    {
        return $this->table()->getAdapter()->fetchOne(
            $this->select($bind)
        );
    }

    /**
     * Fetch one column
     *
     * @param array|null Bindings in array, if they are required in query
     * @return array
     */
    public function fetchCol(array $bind = null) 
    {
        return $this->table()->getAdapter()->fetchCol(
            $this->select($bind)
        );
    }

    /**
     * Delete everything selected
     *
     * You can use it like this:
     *
     * <code>
     * public static function removeByUser(Model_User $user) {
     *     self::retrieve()
     *         ->where('user = ?', $user)
     *         ->delete();
     * }
     * </code>
     *
     * @return FaZend_Db_RowsetWrapper
     * @see Zend_Db_Table_Abstract::delete()
     */
    public function delete() 
    {
        $wheres = $this->select()->getPart(Zend_Db_Select::WHERE);
        $this->table()->delete(implode('', $wheres));
    }

    /**
     * Update everything selected
     *
     * You can use it like this:
     *
     * <code>
     * public static function renameDocuments(Model_User $user) {
     *     self::retrieve()
     *         ->where('user = ?', $user)
     *         ->update(array('name' => new Zend_Db_Expr('UPPER(name)')));
     * }
     * </code>
     *
     * @param array Column-value pairs
     * @return FaZend_Db_RowsetWrapper
     * @see Zend_Db_Table_Abstract::update()
     */
    public function update(array $changes) 
    {
        $wheres = $this->select()->getPart(Zend_Db_Select::WHERE);
        $this->table()->update($changes, implode('', $wheres));
    }

    /**
     * Call wrapping, all other functions will go directly to SELECT object
     *
     * @param string Name of the method to call
     * @param array List of arguments
     * @return FaZend_Db_Wrapper
     */
    public function __call($name, array $args) 
    {
        $this->_select = call_user_func_array(
            array($this->select(), $name), 
            $args
        );
        return $this;
    }

    /**
     * Show SELECT string and die
     *
     * You can use it like this:
     *
     * <code>
     * return self::retrieve()
     *     ->where('user = ?', $user)
     *     ->setRowClass('Model_Pos_Car')
     * //    ->debug()
     *     ->fetchAll();
     * </code>
     *
     * When you un-comment the line with debug(), you will get SELECT string
     * to the output.
     *
     * @return void
     */
    public function debug() 
    {
        bug($this->select()->__toString());
    }
    
}
