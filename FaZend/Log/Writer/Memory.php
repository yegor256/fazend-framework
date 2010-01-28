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
 * Memory logger
 *
 * @package Log
 */
class FaZend_Log_Writer_Memory extends Zend_Log_Writer_Mock
{

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
    public function __construct()
    {
        $this->_formatter = new Zend_Log_Formatter_Simple();
    }

    /**
     * Nothing protocolled yet?
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return count($this->events) == 0;
    }

    /**
     * Convert all events to string
     *
     * @return string
     */
    public function getLog()
    {
        $log = '';
        foreach ($this->events as $event)
            $log .= $this->_formatter->format($event);

        return $log;
    }

}