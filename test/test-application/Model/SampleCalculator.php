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
 * Sample calculator
 *
 * @package application
 * @subpackage Model
 */
class Model_SampleCalculator {
    
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

