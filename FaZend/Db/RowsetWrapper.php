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
     * Table
     *
     * @var Zend_Db_Table
     */
    private $_table;

    /**
     * Select
     *
     * @var Zend_Db_Select
     */
    private $_select;
    
    /**
     * Rowset
     *
     * @var Zend_Db_Table_Rowset
     */
    private $_rowset;

    /**
     * Create a rowset wrapping object
     *
     * @param Zend_Db_Table
     * @param Zend_Db_Select
     * @return void
     */
    public function __construct(Zend_Db_Table $table, Zend_Db_Select $select)
    {
        $this->_table = $table;
        $this->_select = $select;
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
     * Count objects in the rowset
     *
     * @return int Total number of rows in the query
     */
    public function count()
    {
        // We build a new query, where the original query goes into
        // a subquery. You don't need to calculate rows manually
        // and you may be sure that no data goes to memory when
        // you're counting rows. We optimize your request here.
        return $this->_table->getAdapter()->fetchOne(
            sprintf('SELECT COUNT(*) FROM (%s) AS tbl', (string)$this->select()),
            $this->select()->getBind()
        );
    }

    /**
     * Call wrapping, all calls will be forwarded to rowset
     *
     * If the rowset is not used yet, it will be created right now
     *
     * @param string Name of the method to call
     * @param array List of arguments
     * @return mixed
     */
    public function __call($name, array $args)
    {
        if (!isset($this->_rowset))
            $this->_rowset = $this->_table->fetchAll(
                $this->select(), 
                $this->select()->getBind()
            );

        return call_user_func_array(array($this->_rowset, $name), $args);
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
