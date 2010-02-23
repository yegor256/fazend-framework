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
 * Static file delivery from "views/files"
 *
 * @package controllers
 */
class Fazend_FileController extends FaZend_Controller_Action
{

    /**
     * Show one file
     * 
     * @return void
     */
    public function indexAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $file = APPLICATION_PATH . '/views/files/' . $this->_getParam('file');

        // if it's absent
        if (!file_exists($file)) {
            $file = FAZEND_PATH . '/View/files/' . $this->_getParam('file');
            if (!file_exists($file)) {
                $this->getResponse()->setBody('file ' . $this->_getParam('file') . ' not found');
                return;
            }
        }

        // tell browser to cache this content    
        $this->_cacheContent();    

        // set proper type of content
        if (extension_loaded('fileinfo')) {
            $finfo = new finfo(FILEINFO_MIME, '/usr/share/misc/magic');
            if ($finfo) {
                $this->getResponse()->setHeader('Content-type', $finfo->file($file));
            }
        }

        $this->getResponse()->setHeader('Content-Length', filesize($file));
        $this->getResponse()->setBody(file_get_contents($file));
    }    
    
}

