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
 * Complex calculator of "heavy metrics"
 *
 * @package Model
 */
class FaZend_Metric {

    /**
     * Environment proxy
     *
     * @var FaZend_Metric_Proxy_Interface
     */
    protected static $_proxy;

    /**
     * Name of the class
     *
     * @var string
     */
    protected $_class;

    /**
     * Name of the method
     *
     * @var string
     */
    protected $_method;

    /**
     * Associative array of args for the call
     *
     * @var string
     */
    protected $_args = array();

    /**
     * Create new metric to calculate it later
     *
     * @return FaZend_Metric
     */
    public static function create() {

        if (!isset(self::$_proxy))
            self::setProxy(new FaZend_Metric_Proxy());

        return new FaZend_Metric();

    }

    /**
     * Set environment proxy
     *
     * @return void
     */
    public static function setProxy(FaZend_Metric_Proxy_Interface $proxy) {
        self::$_proxy = $proxy;
    }

    /**
     * Calculate the current time and connect the metric to this time
     *
     * When the time comes the metric will be automatically destroyed
     *
     * @return void
     */
    public static function time($interval) {
        // ...
    }

    /**
     * Mark that this metric is dependend on some resource
     *
     * When the resource is destroyed - all metrics connected to this resource
     * will be automatically destroyed
     *
     * @return void
     */
    public static function dependsOn($resource) {
        // ...
    }

    /**
     * Destroy this particular resource and all metrics that depend on it
     *
     * @return void
     */
    public static function destroyResource($resource) {
        // ...
    }

    /**
     * Destroy metrics that match this regexp
     *
     * @return void
     */
    public static function destroyByRegexp($regexp) {
        // ...
    }

    /**
     * Save method name for calculation
     *
     * @return FaZend_Metric
     */
    public function setMethod($method) {
        $this->_method = $method;
        return $this;
    }

    /**
     * Save class name for calculation
     *
     * @return FaZend_Metric
     */
    public function setMethod($class) {
        $this->_class = $class;
        return $this;
    }

    /**
     * Set one argument for calculation
     *
     * @return FaZend_Metric
     */
    public function setArgument($arg, $value) {
        $this->_args[$arg] = $value;
        return $this;
    }

    /**
     * Calculates the value
     *
     * @return string|array|value...
     */
    public function calculate() {
        //...
    }

}
