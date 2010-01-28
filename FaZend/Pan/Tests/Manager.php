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
 * Manager of unit tests
 *
 * @package Pan
 * @subpackage Tests
 */
class FaZend_Pan_Tests_Manager extends FaZend_StdObject
{

    /**
     * Instance of the class
     *
     * @var FaZend_Test_Manager
     */
    protected static $_instance;

    /**
     * Directory with unit tests
     *
     * @var string
     */
    protected $_location;

    /**
     * Instance of the manager
     *
     * @return FaZend_Test_Manager
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new FaZend_Pan_Tests_Manager();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct()
    {
        $this->_location = realpath(APPLICATION_PATH . '/../../test');
    }

    /**
     * Full list of unit tests
     *
     * This method calls protected method recursively
     *
     * @return string[]
     */
    public function getTests()
    {
        return $this->_getTests();
    }

    /**
     * Create a single test runner
     *
     * @param string Name of the unit test file
     * @return FaZend_Pan_Tests_Runner
     */
    public function factory($name)
    {
        return new FaZend_Pan_Tests_Runner($name);
    }
        
    /**
     * Get full list of unit tests, recursively called
     *
     * @param string Directory name, after $this->_location
     * @return string[]
     */
    public function _getTests($path = '.')
    {
        $result = array();
        foreach (glob($this->_location . '/' . $path . '/*') as $file) {
            $matches = array();
            $filePath = $path . '/' . basename($file);

            if (is_dir($file)) {
                $result = array_merge($result, $this->_getTests($filePath));
            } elseif (preg_match('/^(Abstract|\_)/', pathinfo($filePath, PATHINFO_FILENAME))) {
                continue;
            } elseif (preg_match('/\.\/(.*?Test).php$/', $filePath, $matches)) {
                $result[] = $matches[1];
            }
        }

        // reverse sort in order to put directories on top
        rsort($result);

        // return the list of files, recursively
        return $result;
    }

}
