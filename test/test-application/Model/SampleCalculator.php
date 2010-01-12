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

