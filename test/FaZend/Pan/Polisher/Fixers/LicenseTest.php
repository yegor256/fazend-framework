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

require_once 'AbstractTestCase.php';

class FaZend_Pan_Polisher_Fixers_LicenseTest extends AbstractTestCase
{
    
    public static function providerPhpSources()
    {
        $result = "<?php
/**
 * license
 *
 * my two lines
 * license
 *
 * @param test
 */

somecode();
";

        return array(
            array(
                "<?php
/**
 * My project
 * 
 * Some license
 *
 * @param test
 */

somecode();
", $result),

            array(
                "<?php
/**
 * Not enough lines...
 *
 * @param test
 */

somecode();
", $result),

            array(
                "<?php\t
/**\t
 *
 *
 * Some line, which
 * is not correct now
 *\t
 *\t 
 * Another block of text, 
 * which is not correct again
 *\t
 * @param test
 */\t

somecode();
", $result),
        );
    }
    
        public static function providerPhtmlSources()
        {
            $result = "<!--
 *
 * phtml license
 *
 * some license text
 * in two lines
 *
 * @param test
 -->

<p>Test</p>
";

            return array(
                array(
                    "<!--
 *
 * My project
 * 
 * Some license
 *
 * @param test
 -->

<p>Test</p>
", $result),
            );
        }

    /**
     * @dataProvider providerPhpSources
     */
    public function testLicenseCanBeFixedInPhp($origin, $result)
    {
        $fixer = new FaZend_Pan_Polisher_Fixer_License();
        $fixer->setLicense('license', array('my two lines', 'license'));
        
        $fixer->fix($origin, 'php');
        $this->assertEquals($origin, $result, "Failed to process, returned:\n$origin");
    }

    /**
     * @dataProvider providerPhtmlSources
     */
    public function testLicenseCanBeFixedInPhtml($origin, $result)
    {
        $fixer = new FaZend_Pan_Polisher_Fixer_License();
        $fixer->setLicense('phtml license', array('some license text', 'in two lines'));
        
        $fixer->fix($origin, 'phtml');
        $this->assertEquals($origin, $result, "Failed to process, returned:\n$origin");
    }

}
