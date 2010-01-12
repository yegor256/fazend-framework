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
