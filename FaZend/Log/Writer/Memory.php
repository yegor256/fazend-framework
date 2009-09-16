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
 * Memory logger
 *
 * @package FaZend_Log
 */
class FaZend_Log_Writer_Memory extends Zend_Log_Writer_Mock {

    /**
     * Formatter
     *
     * @var Zend_Log_Formatter_Abstract
     */
    protected $_formatter;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct() {
        $this->_formatter = new Zend_Log_Formatter_Simple();
    }

    /**
     * Nothing protocolled yet?
     *
     * @return boolean
     */
    public function isEmpty() {
        return count($this->events) == 0;
    }

    /**
     * Convert all events to string
     *
     * @return string
     */
    public function getLog() {
        $log = '';
        foreach ($this->events as $event)
            $log .= $this->_formatter->format($event) . "\n";

        return $log;
    }

}