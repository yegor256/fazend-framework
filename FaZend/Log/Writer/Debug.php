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
 * Debug logger
 *
 *
 */
class FaZend_Log_Writer_Debug extends Zend_Log_Writer_Abstract
{

    /**
     * Static storage of log messages
     *
     * @var array
     */
    protected static $_events = array();

    /**
     * Nothing protocolled yet?
     *
     * @return boolean
     */
    public static function isEmpty() {
        return count(self::$_events) == 0;
    }

    /**
     * Convert all events to string
     *
     * @return string
     */
    public static function getLog() {
        $log = '';
        foreach (self::$_events as $event)
            $log .= self::_eventText($event) . "\n";

        return $log;
    }

    /**
     * Write a message to the log.
     *
     * @param  array  $event  log data event
     * @return void
     */
    protected function _write($event) {
        self::$_events[] = $event;

        if (APPLICATION_ENV == 'testing')
            echo self::_eventText($event);
    }

    /**
     * Convert event into string
     *
     * @return string
     */
    protected static function _eventText($event) {
        return '[' . $event['priorityName'] . '] ' . $event['message'];
    }

}