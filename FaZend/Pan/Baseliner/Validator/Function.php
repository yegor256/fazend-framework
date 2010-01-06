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
