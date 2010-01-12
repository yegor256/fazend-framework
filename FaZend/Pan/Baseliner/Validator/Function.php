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
 * Validates one file
 *
 * @package Pan
 * @subpackage Baseliner
 */
class FaZend_Pan_Baseliner_Validator_Function extends FaZend_Pan_Baseliner_Validator_Abstract
{

    /**
     * Absolute file name (PHP file)
     *
     * @var string
     **/
    protected $_file;

    /**
     * Name of the function
     *
     * @var string
     **/
    protected $_function;

    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct($file, $function)
    {
        $this->_file = $file;
        $this->_function = $function;
    }
    
    /**
     * Validate existence of the file
     *
     * @return void
     **/
    public function isExists() 
    {
        $path = $this->_location . '/' . $this->_file;
        if (!file_exists($path))
            return "file {$this->_file} is missed, and the function in it: {$this->_function}";
            
        if (!function_exists($this->_function))
            return "function is missed: {$this->_function}";
    }

}
