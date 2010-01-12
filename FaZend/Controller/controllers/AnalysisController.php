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
 * User Interface Modeller
 *
 * @package Pan
 * @subpackage Analysis
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
        
        $diagram = $this->view->diagram = FaZend_Pan_Analysis_Diagram::factory($this->_getParam('diagram'));

    }
    
    /**
     * Show the diagram in SVG/XML format
     *
     * @return void
     */
    public function svgAction() {

        $diagram = FaZend_Pan_Analysis_Diagram::factory($this->_getParam('diagram'));
        
        $this->_helper->layout->disableLayout();
        $this->view->setFilter(null);

        $this->getResponse()
            ->setHeader('Content-type', 'text/xml');
            
        $this->view->svg = $diagram->svg($this->view);

    }
    
}
