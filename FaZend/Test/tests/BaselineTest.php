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
        $map = new FaZend_Pan_Baseliner_Map();
        $validator = new FaZend_Pan_Baseliner_Validator(true);
        
        foreach (new RegexIterator(
            new DirectoryIterator(FaZend_Pan_Baseliner_Map::getStorageDir(true)),
            '/\.xml$/') as $file) {
            $map->load($file);
            $this->assertTrue($validator->validate($map), 
                "Validation failed for $file");
        }
    }

}
