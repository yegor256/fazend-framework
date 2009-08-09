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
 * Manager of unit tests
 *
 * @package FaZend 
 */
class FaZend_Test_Manager extends FaZend_StdObject {

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
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new FaZend_Test_Manager();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct() {
        $this->_location = realpath(APPLICATION_PATH . '/../../test');
    }

    /**
     * Full list of unit tests
     *
     * This method calls protected method recursively
     *
     * @return string[]
     */
    public function getTests() {
        return $this->_getTests();
    }

    /**
     * Create a single test runner
     *
     * @param string Name of the unit test file
     * @return FaZend_Test_Runner
     */
    public function factory($name) {
        return new FaZend_Test_Runner($name);
    }
        
    /**
     * Get full list of unit tests, recursively called
     *
     * @param string Directory name, after $this->_location
     * @return string[]
     */
    public function _getTests($path = '.') {

        $result = array();
        foreach (glob($this->_location . '/' . $path . '/*') as $file) {

            $matches = array();
            $filePath = $path . '/' . basename($file);

            if (is_dir($file))
                $result = array_merge($result, $this->_getTests($filePath));
            elseif (preg_match('/\.\/(.*?Test).php$/', $filePath, $matches))
                $result[] = $matches[1];
        }

        // reverse sort in order to put directories on top
        rsort($result);

        // return the list of files, recursively
        return $result;

    }

}
