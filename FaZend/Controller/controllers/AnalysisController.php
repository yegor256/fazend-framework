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
 * User Interface Modeller
 *
 * @category FaZend
 * @package controllers
 */
class Fazend_AnalysisController extends FaZend_Controller_Panel {

    /**
     * Sanity check before dispatching
     *
     * @return void
     */
    public function preDispatch() {
        
        // sanity check
        if (APPLICATION_ENV == 'production')
            $this->_redirectFlash('Analysis controller is not allowed in production environment', 'restrict', 'login');
        
        parent::preDispatch();

    }

    /**
     * Show the entire map of the system
     *
     * @return void
     */
    public function indexAction() {
        
        $diagram = $this->view->diagram = FaZend_AnalysisModeller_Diagram::factory($this->_getParam('diagram'));

    }
    
    /**
     * Show the diagram in SVG/XML format
     *
     * @return void
     */
    public function svgAction() {

        $diagram = FaZend_AnalysisModeller_Diagram::factory($this->_getParam('diagram'));
        
        $this->_helper->layout->disableLayout();
        $this->view->setFilter(null);

        $this->getResponse()
            ->setHeader('Content-type', 'text/xml');
            
        $this->view->svg = $diagram->svg($this->view);

    }
    
}
