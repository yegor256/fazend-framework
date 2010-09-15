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

require_once 'FaZend/Controller/Action.php';

/**
 * Deliver one CSS stylesheet, located in "scripts/css".
 *
 * @package controllers
 */
class Fazend_CssController extends FaZend_Controller_Action
{

    /**
     * Shall we compress CSS?
     *
     * @var boolean
     * @see setCompression()
     */
    protected static $_compress = false;

    /**
     * Shall we compress CSS?
     *
     * @param bool
     * @return void
     */
    public static function setCompression($compress = true) 
    {
        self::$_compress = $compress;
    }

    /**
     * Show one CSS stylesheet.
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->getResponse()
            ->setHeader('Content-Type', 'text/css')
            ->setHeader('Cache-Control', 'public, max-age=315360000');

        // change location of view scripts
        $this->_helper->viewRenderer
            ->setViewScriptPathSpec(':controller/' . $this->_getParam('css'));
        
        // don't use HTML layouts
        $this->_helper->layout->disableLayout();

        // remove all other compression
        $this->view->setFilter(null);

        // inject CSS compressor
        if (self::$_compress) {
            $this->view->addFilter('CssCompressor');
        }

        $this->_helper->viewRenderer($this->_getParam('css'));
    }    

}

