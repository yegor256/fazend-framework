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
class FaZend_Pan_Baseliner_Validator_Class extends FaZend_Pan_Baseliner_Validator_Abstract
{

    /**
     * Class name
     *
     * @var string
     **/
    protected $_class;

    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct($class)
    {
        $this->_class = $class;
    }
    
    /**
     * Validate existence of the file
     *
     * @return string|null
     **/
    public function isExists() 
    {
        if (!class_exists($this->_class))
            return "class {$this->_class} is absent";
    }

    /**
     * Validate inheritance
     *
     * @return string|null
     **/
    public function isInstanceOf($parent) 
    {
        if (!is_subclass_of($this->_class, $parent))
            return "class {$this->_class} is not {$parent}";
    }

}
