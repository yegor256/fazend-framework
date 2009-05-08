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
 * Paginator
 *
 * @package FaZend 
 */
class FaZend_Paginator extends Zend_Paginator {

        /**
         * Add paginator to the view
         *
         * @return void
         */
        public static function addPaginator($iterator, Zend_View $view, $page) {
        	$paginator = new FaZend_Paginator(new Zend_Paginator_Adapter_Iterator($iterator));
        	
        	$paginator->setView($view);
        	$paginator->setItemCountPerPage(10);
		$paginator->setCurrentPageNumber($page);

        	$view->paginator = $paginator;

        }

}
