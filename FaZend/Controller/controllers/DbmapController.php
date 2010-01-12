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
