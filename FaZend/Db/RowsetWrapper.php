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
 * Wrapper for fetchAll method
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 * @package Db
 */
class FaZend_Db_RowsetWrapper implements SeekableIterator, Countable, ArrayAccess
{

    /**
     * Select
     *
     * @var Zend_Db_Select
     * @see __construct()
     */
    private $_select;
    
    /**
     * Table
     *
     * @var Zend_Db_Table
     * @see __construct()
     */
    private $_table;

    /**
     * Rowset
     *
     * @var Zend_Db_Table_Rowset|null NULL means that there is no rowset yet
     */
    private $_rowset = null;

    /**
     * Create a rowset wrapping object
     *
     * @param Zend_Db_Select
     * @param Zend_Db_Table
     * @return void
     */
    public function __construct(Zend_Db_Select $select, Zend_Db_Table $table)
    {
        $this->_select = $select;
        $this->_table = $table;
    }

    /**
     * Returns select object
     *
     * @return Zend_Db_Select
     */
    public function select()
    {
        return $this->_select;
    }

    /**
     * Count objects in the rowset.
     *
     * @return int Total number of rows in the query
     */
    public function count()
    {
        $select = clone $this->select();
        $select->reset(Zend_Db_Select::COLUMNS)
            ->columns(new Zend_Db_Expr('COUNT(*)'));
        try{
            $cnt = $this->_table->getAdapter()->fetchOne(
                $select,
                $this->select()->getBind()
            );
        } catch (Zend_Db_Statement_Exception $e) {
            FaZend_Exception::raise(
                'FaZend_Db_RowsetWrapper_Exception', 
                sprintf(
                    '%s: (%s) in "%s"',
                    get_class($e),
                    $e->getMessage(),
                    strval($select)
                )
            );
        }
        return $cnt;
    }

    /**
     * Call wrapping, all calls will be forwarded to rowset
     *
     * If the rowset is not used yet, it will be created right now
     *
     * @param string Name of the method to call
     * @param array List of arguments
     * @return mixed
     * @throws FaZend_Db_RowsetWrapper_Exception
     */
    public function __call($name, array $args)
    {
        // If rowset is not yet ready we should retrieve it now
        // from the DB. This is a lazy loading mechanism implemented
        // here to avoid immediate retrieval of rowsets
        if (!isset($this->_rowset)) {
            try {
                $this->_rowset = $this->_table->fetchAll(
                    $this->select(), 
                    $this->select()->getBind()
                );
            } catch (Zend_Db_Statement_Exception $e) {
                FaZend_Exception::raise(
                    'FaZend_Db_RowsetWrapper_Exception', 
                    sprintf(
                        '%s: (%s) in "%s"',
                        get_class($e),
                        $e->getMessage(),
                        $this->select()
                    )
                );
            }
        }

        // execute what was asked
        return call_user_func_array(
            array($this->_rowset, $name), 
            $args
        );
    }

    /**
     * Method wrapping
     *
     * @param mixed Position
     * @return mixed
     */
    public function seek($position)
    {
        return $this->__call('seek', array($position));
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function key()
    {
        return $this->__call('key', array());
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function next()
    {
        return $this->__call('next', array());
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function current()
    {
        return $this->__call('current', array());
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function valid()
    {
        return $this->__call('valid', array());
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function rewind()
    {
        return $this->__call('rewind', array());
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function offsetExists($offset)
    {
        return $this->__call('offsetExists', array($offset));
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->__call('offsetGet', array($offset));
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        return $this->__call('offsetSet', array($offset, $value));
    }

    /**
     * Method wrapping
     *
     * @return mixed
     */
    public function offsetUnset($offset)
    {
        return $this->__call('offsetUnset', array($offset));
    }

}
