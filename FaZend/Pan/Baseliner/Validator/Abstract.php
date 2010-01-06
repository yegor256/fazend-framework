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
