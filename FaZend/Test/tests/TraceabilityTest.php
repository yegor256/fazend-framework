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

require_once 'FaZend/Test/TestCase.php';

/**
 * Test uni-directional traceability between components
 *
 * @package Test
 */
class FaZend_Test_tests_TraceabilityTest extends FaZend_Test_TestCase
{
    
    /**
     * Check traceability
     *
     * @return void
     */
    public function testUniDirectionalTraceabilityIsEstablished()
    {
        $facade = new FaZend_Pan_Analysis_Facade();
        $list = $facade->getComponentsList();
        
        for ($i=0; $i<5; $i++) {
            foreach ($list as $id=>$component) {
                $hasForeignLinks = false;
                foreach ($component['traces'] as $trace) {
                    if (strpos($trace, FaZend_Pan_Analysis_Component_System::ROOT) === 0) {
                        $hasForeignLinks = true;
                    }
                    if (!isset($list[$trace])) {
                        $hasForeignLinks = true;
                    }
                }
                if (!in_array($component['type'], array('class', 'method'))) {
                    $hasForeignLinks = true;
                }
                if ($hasForeignLinks) {
                    unset($list[$id]);
                }
            }
        }
        
        foreach ($list as $component) {
            logg("%s '%s' misses uplink", ucfirst($component['type']), $component['tag']);
        }
        
        if (!empty($list)) {
            logg('%d components miss uplinks', count($list));
            $this->markTestIncomplete();
        }
    }

}
