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
 * sample class for htmlTable testing
 * 
 *
 * @package application
 * @subpackage Model
 * @see Model_Owner
 */
class Model_Owner_Details extends FaZend_StdObject
{

    /**
     * @see Model_Owner_Details
     */
    public function __toString()
    {
        return "name: " . $this->name;
    }

}


