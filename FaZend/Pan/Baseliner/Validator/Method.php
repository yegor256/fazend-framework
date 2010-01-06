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
class FaZend_Pan_Baseliner_Validator_Method extends FaZend_Pan_Baseliner_Validator_Abstract
{

    /**
     * Class name
     *
     * @var string
     **/
    protected $_class;
    
    /**
     * Name of the method in the class
     *
     * @var string
     **/
    protected $_method;

    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct($class, $method)
    {
        $this->_class = $class;
        $this->_method = $method;
    }
    
    /**
     * Validate existence of the method in the class
     *
     * @return void
     **/
    public function isExists() 
    {
        if (!method_exists($this->_class, $this->_method))
            return "method {$this->_class}::{$this->_method} is absent";
    }

}
