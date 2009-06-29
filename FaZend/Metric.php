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
     * Associative array of metrics running now
     *
     * @var array
     */
    protected static $_calculatingNow = array();

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
     * Numbered array of args for the call
     *
     * @var array
     */
    protected $_args = array();

    /**
     * Create new metric and calculates it now
     *
     * @param string Name of the variable to be set, if metric is found
     * @return FaZend_Metric
     */
    public static function calculate($variableName = 'metricValue') {

        $metric = new FaZend_Metric();

        // we get parameters of the caller from the backtrace array
        $backtrace = debug_backtrace();
        $metric->setClass($backtrace[1]['object'])
            ->setMethod($backtrace[1]['function'])
            ->setArguments($backtrace[1]['args']);

        // if we are now calculating this metric?
        // we should skip the process and return false
        // normal execution in the METHOD will continue
        if ($metric->_isCalculatingNow())
            return false;

        // if the metric already exists in cache,
        // we just load the value and return it
        if ($metric->exists()) {
            eval("\$value = \$metric->load();");
        } else {
            eval("\$value = \$metric->save(\$metric->_calculate());");
        }

        // save variable name to the class variable
        $metric->getClass()->$variableName = $value;

        // return TRUE in any case
        return true;
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
     * Get environment proxy and initialize it, if necessary
     *
     * @return void
     */
    public static function getProxy() {
        if (!isset(self::$_proxy))
            self::setProxy(new FaZend_Metric_Proxy());

        return self::$_proxy;
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
     * Save class for calculation
     *
     * @return FaZend_Metric
     */
    public function setClass($class) {
        $this->_class = $class;
        return $this;
    }

    /**
     * Returns class used for calculation
     *
     * @return class
     */
    public function getClass() {
        return $this->_class;
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
     * Returns method name for calculation
     *
     * @return string
     */
    public function getMethod() {
        return $this->_method;
    }

    /**
     * Set array of arguments for calculation
     *
     * @param array Numbered array of function parameters
     * @return FaZend_Metric
     */
    public function setArguments($args) {
        $this->_args = $args;
        return $this;
    }

    /**
     * Loads the value of the metric from cache
     *
     * @return string|array|value...
     */
    public function load() {
        //...
    }

    /**
     * Saves the value of the metric to cache
     *
     * @return void
     */
    public function save($value) {
        //...

        // return the value, just saved to cache
        return $value;
    }

    /**
     * Metric exists in cache?
     *
     * @return boolean
     */
    public function exists() {
        //...
        return false;
    }

    /**
     * Calculate the actual value of the metric
     *
     * @return value
     */
    public function _calculate() {
        self::$_calculatingNow[$this->_uniqueId()] = true;
        $value = call_user_func_array(array($this->_class, $this->_method), $this->_args);
        unset(self::$_calculatingNow[$this->_uniqueId()]);
        return $value;
    }

    /**
     * Is calculating now?
     *
     * @return boolean
     */
    public function _isCalculatingNow() {
        return isset(self::$_calculatingNow[$this->_uniqueId()]);
    }

    /**
     * Unique ID of the metric
     *
     * @return string
     */
    public function _uniqueId() {
        return get_class($this->_class) . '::' . $this->_method . '()';
    }

}
