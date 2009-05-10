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
 * Representative for a single SQL table
 *
 * @see http://framework.zend.com/manual/en/zend.db.table.html
 */
class FaZend_Table_User extends Zend_DB_Table_Abstract {

        /**
         * Name of the table in SQL database
         *
         * @var string
         */
	protected $_name = 'user';

        /**
         * Name of the primary key in the table
         *
         * @var string
         */
	protected $_primary = 'id';

	/**
	 * Classname for row
	 *
	 * @var string
	 */
	protected $_rowClass = 'FaZend_User';

}
