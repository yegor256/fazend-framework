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
		return pow($base, $pow);
	}
	function getDependentValue() {
		FaZend::dependsOn('files');
		return rand();
	}
	function getParentValue() {
		$value = FaZend_Metric::create()
			->setMethod('getDependentValue')
			->setClass(new SomeClass())
			->calculate();

		return rand();
	}
}

class FaZend_MetricTest extends AbstractTestCase {

	/*	
	public function testSimpleScenarioWorks () {

		$value = FaZend_Metric::create()
			->setMethod('getBigValue')
			->setClass(new SomeClass())
			->setArgument('base', 2)
			->setArgument('pow', 6)
			->calculate();

		$this->assertEquals(64, $value, 'Failed to calculate, why?');	
	}

	public function testResourceDependencyWorks () {

		$value1 = FaZend_Metric::create()
			->setMethod('getDependentValue')
			->setClass(new SomeClass())
			->calculate();

		FaZend_Metric::destroyResource('files');	

		$value2 = FaZend_Metric::create()
			->setMethod('getDependentValue')
			->setClass(new SomeClass())
			->calculate();

		$this->assertNotEquals($value1, $value2, 'Values are the same, why?');	
	}

	public function testMetric2MetricDependencyWorks () {

		$value1 = FaZend_Metric::create()
			->setMethod('getParentValue')
			->setClass(new SomeClass())
			->calculate();

		FaZend_Metric::destroyResource('files');	

		$value2 = FaZend_Metric::create()
			->setMethod('getParentValue')
			->setClass(new SomeClass())
			->calculate();

		$this->assertNotEquals($value1, $value2, 'Values are the same, why?');	
	}
	*/

}
