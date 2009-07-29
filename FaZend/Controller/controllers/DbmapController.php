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
 *
 */
class Fazend_DbmapController extends FaZend_Controller_Action {

    /**
     * Show the map in PNG file
     *
     * @return void
     */
    public function indexAction() {

        // create new map builder
        $map = new FaZend_Deployer_Map();

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
        $map = new FaZend_Deployer_SingleTable($this->_getParam('name'));

        // return PNG
        $this->_returnPNG($map->png());

    }

}
