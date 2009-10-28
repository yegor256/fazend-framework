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
 * Map of the database
 *
 * @package Pan
 * @subpackage Database
 */
class Fazend_DbmapController extends FaZend_Controller_Panel {

    /**
     * Sanity check before dispatching
     *
     * @return void
     */
    public function preDispatch() {
        
        // sanity check
        if (APPLICATION_ENV == 'production')
            $this->_redirectFlash('DBMAP controller is not allowed in production environment', 'restrict', 'login');
        
        parent::preDispatch();

    }

    /**
     * Show the map in PNG file
     *
     * @return void
     */
    public function indexAction() {

        // create new map builder
        $map = new FaZend_Pan_Database_Map();

        // return PNG
        $this->_returnPNG($map->png());

    }

    /**
     * Show one table in PNG file
     *
     * @return void
     */
    public function tableAction() {

        // create single table builder
        $map = new FaZend_Pan_Database_SingleTable($this->_getParam('name'));

        // return PNG
        $this->_returnPNG($map->png());

    }

}
