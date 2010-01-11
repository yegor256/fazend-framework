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

require_once 'AbstractTestCase.php';

class FaZend_Pan_Analysis_FacadeTest extends AbstractTestCase
{
    
    public function testListOfComponentsIsAccessible()
    {
        $facade = new FaZend_Pan_Analysis_Facade();
        $list = $facade->getComponentsList();
        $component = array_pop($list);
        logg(print_r($component, true));
    }

}
