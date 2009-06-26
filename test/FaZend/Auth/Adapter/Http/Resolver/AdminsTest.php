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

class FaZend_Auth_Adapter_Http_Resolver_AdminsTest extends AbstractTestCase {
    
    public function testResolveWorks () {

        $resolver = new FaZend_Auth_Adapter_Http_Resolver_Admins();

        $credential = $resolver->resolve('super', 'adm');

        $this->assertNotEquals(false, $credential, "Empty password, why?");

    }

}
        