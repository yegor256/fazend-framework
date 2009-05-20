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

// ORM auto-mapping classes
class Owner extends FaZend_Db_Table_ActiveRow_owner {
	function isMe() {
		return true;
	}
}

class Product extends FaZend_Db_Table_ActiveRow_product {}

