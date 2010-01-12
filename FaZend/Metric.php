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
 * Complex calculator of "heavy metrics"
 *
 * One instance of this class is a ready-to-calculate metric, which will
 * be calculated right now or will get its value from storage (database).
 * The class is created ONLY by internal method ::calculate(), and only as
 * an internal call from some "heavy" method, e.g.:
 *
 * <code>
 * class ComplexClass {
 *   function heavyCalculation() {
 *     if (Metric::calculate())
 *       return $this->metricValue;
 *
 *     // heavy calculation
 *     return $bigAndComplexValue;
 *   }
 * }
 * class CallingClass {
 *   function getValue() {
 *     $class = new ComplexClass();
 *     $bigValue = $class->heavyCalculation(); // will calculate the value ONLY once
 *   }
 * }
 * </code>
 *
 * In the code above the method heavyCalculation() will be terminated at the
 * beginning, if the value was calculated before and exists in the database. In such
 * a case the method ::calculate() will return TRUE and will set the value of $this->metricValue
 * to the result calculated before.
 *
 * This approach is better than Zend_Cache mostly becuase the control of 
 * load balancing is done by executors, not by callers. In other words, method getValue()
 * doesn't know whether the calculation inside heavyCalculation() is heavy or not.
 * The calculator/executor itself is responsible for the caching procedures.
 *
 * Technically, the method heavyCalculation() will be called twice:
 *  1. by method getValue()
 *  2. by class FaZend_Metric created in ::calculate()
 *
 * Second call of the method is optional and will be done only if the value
 * is not stored yet in the database.
 *
 * @package Metric
 */
class FaZend_Metric {

    /**
     * Associative array of metrics running now
     *
     * @var array
     */
    protected static $_calculatingNow = array();

    /**
     * Instance of the class to be called
     *
     * @var instance
     */
    protected $_class;

    /**
     * Name of the method to be called in $this->_class
     *
     * @var string
     */
    protected $_method;

    /**
     * Numbered array of args for the call, to be sent into call_user_func_array()
     *
     * @var array
     */
    protected $_args = array();

    /**
     * Protected constructor in order to disable class instantiating from outside classes
     *
     * You should not (and you can't) instantiate this class directly. Only
     * during a call to the ::calculate() static method.
     *
     * @return void
     */
    protected function __construct() {
    }

    /**
     * Create new metric and load/calculate it now
     *
     * The method creates new instance of FaZend_Metric, loads it with 
     * information about "caller" and then decide what to do. There are a number
     * of scenarios:
     *
     * 1. "We're already calculating this metric now". It means that the call
     * to this method came from the FaZend_Metric itself. We should just return 
     * false and allow the normal execution of the method operations. These operations
     * will result is a return of some value, which is the value of the metric.
     *
     * 2. "Metric value already exists in the storage (database)". We should not
     * calculate it again, just load from the storage and return.
     *
     * 3. "Metric value does NOT exist yet". We should call the method again
     * in order to get the value. Then we should save it to the database. And then
     * we should save it to the variable in the "calculator class". Once it is done
     * we should return TRUE and the calculator will return the value to the caller.
     *
     * @param string Name of the variable to be set inside $this->_class, if metric is found
     * @return FaZend_Metric
     */
    public static function calculate($variableName = 'metricValue') {

        // create new metric class instance and configure it
        $metric = new FaZend_Metric();

        // we get parameters of the caller from the backtrace array
        // this is maybe not the best approach, but the only one 
        // available in PHP 5.2
        $backtrace = debug_backtrace();
        $metric->_setClass($backtrace[1]['object'])
            ->_setMethod($backtrace[1]['function'])
            ->_setArguments($backtrace[1]['args']);

        // if we are now calculating this metric?
        // we should skip the process and return false
        // normal execution in the METHOD will continue.
        // this will mean that there is a SECOND call to the method,
        // done by the FaZend_Metric class itself
        if ($metric->_isCalculatingNow())
            return false;

        // if the metric already exists in cache,
        // we just load the value and return it
        if ($metric->_exists()) {
            $value = $metric->_load();
        } else {
            // otherwise we calculate it from scratch, save the value
            // and return it
            $value = $metric->_save($metric->_calculate());
        }

        // save variable name to the class instance
        $metric->_getClass()->$variableName = $value;

        // return TRUE in any case
        return true;
    }

    /**
     * Calculate the current time and connect the metric to this time
     *
     * When the time comes the metric will be automatically destroyed. This
     * method uses method dependsOn(), providing the unique identifier
     * time-specific resource. This resource expires on time, inside the
     * method create(). In other words, every time you go for some metric,
     * the storage loses some metrics which are expired.
     *
     * @param int Interval in seconds when the source should expire
     * @return void
     * @see dependsOn()
     */
    public static function time($interval) {
        // ...
    }

    /**
     * Mark that this metric is dependend on some resource
     *
     * When the resource is destroyed - all metrics connected to this resource
     * will be automatically destroyed. The method should be called inside
     * the calculator, e.g.:
     *
     * <code>
     * class ComplexClass {
     *   function heavyCalculation() {
     *     if (Metric::calculate())
     *       return $this->metricValue;
     *
     *     FaZend_Metric::dependsOn('file'); // our calculations depend on this resource
     *
     *     // heavy calculation
     *     return $bigAndComplexValue;
     *   }
     * }
     * class CallingClass {
     *   function getValue() {
     *     $class = new ComplexClass();
     *     $bigValue = $class->heavyCalculation(); // calculated first time
     *     FaZend_Metric::destroyResource('file'); // kill the value of just-calculated metric
     *     $bigValue = $class->heavyCalculation(); // calculated again!
     *   }
     * }
     * </code>
     *
     * This mechanism is very similar to Zend_Cache tags.
     *
     * @param string Name of resource (unique in the storage!)
     * @return void
     */
    public static function dependsOn($resource) {
        // ...
    }

    /**
     * Destroy this particular resource and all metrics that depend on it
     *
     * @param string Name of resource (unique in the storage!)
     * @return void
     * @see dependsOn()
     */
    public static function destroyResource($resource) {
        // ...
    }

    /**
     * Destroy metrics that match this regexp
     *
     * @param string Regular expression on the ID of the metric
     * @return void
     * @see _uniqueID()
     */
    public static function destroyByRegexp($regexp) {
        // ...
    }

    /**
     * Save class for calculation
     *
     * @param instance Instance of some class, the calculator
     * @return FaZend_Metric
     */
    protected function _setClass($class) {
        $this->_class = $class;
        return $this;
    }

    /**
     * Returns class used for calculation
     *
     * @return class
     */
    protected function _getClass() {
        return $this->_class;
    }

    /**
     * Save method name for calculation
     *
     * @return FaZend_Metric
     */
    protected function _setMethod($method) {
        $this->_method = $method;
        return $this;
    }

    /**
     * Returns method name for calculation
     *
     * @return string
     */
    protected function _getMethod() {
        return $this->_method;
    }

    /**
     * Set array of arguments for calculation
     *
     * @param array Numbered array of function parameters
     * @return FaZend_Metric
     */
    protected function _setArguments($args) {
        $this->_args = $args;
        return $this;
    }

    /**
     * Loads the value of the metric from cache
     *
     * @return string|array|value...
     */
    protected function _load() {
        //...
    }

    /**
     * Saves the value of the metric to cache
     *
     * @return void
     */
    protected function _save($value) {
        //...

        // return the value, just saved to cache
        return $value;
    }

    /**
     * Metric exists in cache?
     *
     * @return boolean
     */
    protected function _exists() {
        //...
        return false;
    }

    /**
     * Calculate the actual value of the metric
     *
     * We assume that the calculation of a heavy method may consume resources (time and memory).
     * Sometimes it's more efficient to perform this calculation in a separate process.
     * This is even more important when hosting environment doesn't allow to run 
     * unlimited-time scripts. Memory consumption is the second problem. memory_limit=-1 is
     * a disabled option very often.
     *
     * This method decides what is the best way to calculate the value of the metric:
     *  1. immediately in this process/thread
     *  2. in a new process created by HTTP call to itself
     *
     * In the second option the call is done by HTTP to the MetricController, which 
     * understands what should be calculated, gets the serialized copy of $this->_class
     * and calculates the value of the metric. The value is returned as a serialized result,
     * in plain/text.
     *
     * @return value
     */
    protected function _calculate() {

        // to protect against an endless recursive call of one and the same
        // metric we should set this flag and reset it right after
        // the calculation is done
        self::$_calculatingNow[$this->_uniqueId()] = true;
        $value = call_user_func_array(array($this->_class, $this->_method), $this->_args);
        unset(self::$_calculatingNow[$this->_uniqueId()]);

        // return what we've just calculated
        return $value;

    }

    /**
     * Is calculating now?
     *
     * @return boolean
     */
    protected function _isCalculatingNow() {
        return isset(self::$_calculatingNow[$this->_uniqueId()]);
    }

    /**
     * Unique ID of the metric, which should be unique on the entire application
     *
     * We assume that class name and method name together will be unique
     * in any particular application. If this is not true, this method
     * could be overridden by a daughter class.
     *
     * @return string
     */
    protected function _uniqueId() {
        return get_class($this->_class) . '::' . $this->_method . '()';
    }

}
