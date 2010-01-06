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
class FaZend_Pan_Baseliner_Validator_File extends FaZend_Pan_Baseliner_Validator_Abstract
{

    /**
     * Absolute file name (PHP file)
     *
     * @var string
     **/
    protected $_file;

    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct($file)
    {
        $this->_file = $file;
    }
    
    /**
     * Validate existence of the file
     *
     * @return string|null
     **/
    public function isExists() 
    {
        if (!file_exists($this->_location . '/' . $this->_file))
            return "file is absent: {$this->_file}";
    }

}
