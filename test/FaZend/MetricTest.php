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

require_once 'AbstractTestCase.php';

/**
 * FaZend_Metric_SampleCalculator
 */
require_once 'Metric/SampleCalculator.php';

/**
 * Metric tester
 *
 * @package test
 */
class FaZend_MetricTest extends AbstractTestCase {

    /**
     * Sample metrics usage scenario
     *
     * We have a calculator, which calculates some big value (sample). We
     * call this method and get a result. We don't know anything about metrics
     * behind this call. And the call works.
     *
     * @return void
     */
    public function testSimpleScenarioWorks() {

        $calculator = new FaZend_Metric_SampleCalculator();

        // we suppose to get the result of pow(2,6)
        $value = $calculator->getBigValue(2, 6);
        
        $this->assertEquals(64, $value, 'Failed to calculate, why?');    

    }

    /**
     * Metrics may depend on resources
     *
     * We call a metric, which will call FaZend_Metric::dependsOn() inside.
     * Then we destroy the resource and the metric should die as well.
     *
     * @return void
     */
    public function testResourceDependencyWorks() {

        $calculator = new FaZend_Metric_SampleCalculator();

        $value1 = $calculator->getDependentValue();

        FaZend_Metric::destroyResource('files');    

        $value2 = $calculator->getDependentValue();

        //$this->assertNotEquals($value1, $value2, 'Values are the same, why?');    

    }

    /**
     * Metrics may depend on other metrics
     *
     * We call a metric, which will call another metric inside.
     * Then we destroy the resource, which the second metric depends on.
     * The first metric should die as well.
     *
     * @return void
     */
    public function testMetric2MetricDependencyWorks() {

        $calculator = new FaZend_Metric_SampleCalculator();

        $value1 = $calculator->getParentValue();

        FaZend_Metric::destroyResource('files');    

        $value2 = $calculator->getParentValue();

        //$this->assertNotEquals($value1, $value2, 'Values are the same, why?');    
    }

    /**
     * Syntax error inside metric (or any other PHP error)
     *
     * We started to calculate some metric and discovered a syntax
     * error inside it's code. We should properly understand this
     * situation and don't store anything in the Db. When this metric
     * is called again, we should try to calculate it again.
     *
     * @return void
     */
    public function testSyntaxErrorInsideMetricIsProcessedCorrectly() {
        // ...
    }

    /**
     * Exception inside metric
     *
     * When and if an Exception is raised inside the method which calculates
     * the metric value, we should pass it correctly to the caller. And when
     * this metric is called again, we should try to calculate it again.
     *
     * @return void
     */
    public function testExceptionsAreProcessedCorrectly() {
        // ...
    }

    /**
     * Database failure in metric storage won't cause problems
     *
     * Imagine the situation when we calculated the metric value, some dependencies
     * and sub-metrics. And then we failed to store some information to the 
     * database. We should return value correctly and make sure that
     * the next time the metric will be calculated again.
     *
     * @return void
     */
    public function testDatabaseCrashDoesntCauseTroubles() {
        // ...
    }

    /**
     * Table for metric is checked automatically
     *
     * If the tables are absent in the database for metric storage
     * we should signal to admin by email and continue normal working
     * without metrics.
     *
     * @return void
     */
    public function testAbsentOrCorruptTablesDontTerminateWork() {
        // ...
    }

    /**
     * Timeout doesn't cause problems
     *
     * If during metric calculation we get a timeout for the script,
     * we should correctly process the situation. And when the script
     * is called again we should restart the metric calculation, if
     * we failed to do it first time. If we successfully calculated
     * the metric value from the first attempt - we should use its
     * value now.
     *
     * @return void
     */
    public function testTimeoutWontKillSuccessfulMetrics() {
        // ...
    }

    /**
     * Double calculation is prohibited
     *
     * We start one metric calculation and another process/script starts
     * the same metric calculation, until the first metric is finished.
     * We have to calculate the metric ONLY once, the second script will
     * just wait until the first one finishes and returns its value.
     *
     * @return void
     */
    public function testConcurrentCallToAMetricWontLeadToDoubleCalculation() {
        // ...
    }

    /* this section is for "forked" metrics */
    
    /**
     * Failed metrics should be recalculated if possible
     *
     * When we calculate a metric in a forked mode, we may fail for some
     * reason (network crash for example). We should try to recalculate it
     * several times. Until we crash totally or we get a result.
     *
     * @return void
     */
    public function testFailedForkedCalculationsAreRepeated() {
        // ...
    }

}
