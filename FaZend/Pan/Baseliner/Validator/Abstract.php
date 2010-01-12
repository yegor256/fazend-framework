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
 * Validates one entity
 *
 * @package Pan
 * @subpackage Baseliner
 */
abstract class FaZend_Pan_Baseliner_Validator_Abstract
{

    /**
     * Location of application to validate
     *
     * @var string
     **/
    protected $_location;
    
    /**
     * Set location
     *
     * @param string Location to set
     * @return void
     **/
    public function setLocation($location) 
    {
        $this->_location = $location;
    }

}
