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
