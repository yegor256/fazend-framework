<?php
/**
 *
 * Copyright (c) 2009, FaZend.com
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of FaZend.com. located at
 * www.FaZend.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@FaZend.com
 *
 * @copyright Copyright (c) FaZend.com, 2009
 * @version $Id$
 *
 */

/**
 * Get SVN revision
 *
 * @package FaZend 
 */
class FaZend_Revision {

        /**
         * Cut long line
         *
         * @return string
         */
	public static function get () {

		$revFile = APPLICATION_PATH.'/deploy/subversion/revision.txt';

		return (file_exists ($revFile) ? file_get_contents ($revFile) : 'local');	

	}

}
