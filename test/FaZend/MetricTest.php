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

class SomeClass {
    
    function getBigValue($base, $pow) {
        if (FaZend_Metric::calculate())
            return $this->metricValue;

        return pow($base, $pow);
    }

    function getDependentValue() {
        if (FaZend_Metric::calculate())
            return $this->metricValue;

        FaZend_Metric::dependsOn('files');
        return rand();
    }

    function getParentValue() {
        if (FaZend_Metric::calculate())
            return $this->metricValue;

        $value = $this->getDependentValue();

        return rand();
    }

}

class FaZend_MetricTest extends AbstractTestCase {

    public function setUp() {
        parent::setUp();
        $this->cls = new SomeClass();
    }

    public function testSimpleScenarioWorks () {
        $value = $this->cls->getBigValue(2, 6);
        $this->assertEquals(64, $value, 'Failed to calculate, why?');    
    }

    public function testResourceDependencyWorks () {
        $value1 = $this->cls->getDependentValue();

        FaZend_Metric::destroyResource('files');    

        $value2 = $this->cls->getDependentValue();

        //$this->assertNotEquals($value1, $value2, 'Values are the same, why?');    
    }

    public function testMetric2MetricDependencyWorks () {

        $value1 = $this->cls->getParentValue();

        FaZend_Metric::destroyResource('files');    

        $value2 = $this->cls->getParentValue();

        //$this->assertNotEquals($value1, $value2, 'Values are the same, why?');    
    }

}
