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
 * Test conformance to specified baselines
 *
 * @package Test
 */
class BaselineTest extends FaZend_Test_TestCase
{
    
    /**
     * Test entire project for conformance to baselines
     *
     * @return void
     **/
    public function testCodeConformsToBaselines()
    {
        $validator = new FaZend_Pan_Baseliner_Validator(APPLICATION_PATH, true);
        $dir = FaZend_Pan_Baseliner_Map::getStorageDir(false);
        
        if (!file_exists($dir) || !is_dir($dir)) {
            logg("Directory with baselines ($dir) is absent");
            return;
        }
        
        foreach (new RegexIterator(new DirectoryIterator($dir), '/\.xml$/') as $file) {
            $path = $dir . '/' . $file;
                
            $map = new FaZend_Pan_Baseliner_Map(APPLICATION_PATH, $path);
            $map->load($path);
            $this->assertTrue(
                $validator->validate($map), 
                "Validation failed for {$file}"
            );
        }
    }

}
