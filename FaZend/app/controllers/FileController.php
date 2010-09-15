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
 * @see FaZend_Controller_Action
 */
require_once 'FaZend/Controller/Action.php';

/**
 * Static file delivery from "views/files"
 *
 * The controller is hooked to the route "files" and accepts two params. First
 * one is mandatory and is a relative name of the file to deliver. The second
 * one is optional and instructs us whether we should RENDER the file or send it
 * "as is".
 *
 * @package controllers
 * @see routes.ini
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
        // shall we use Zend_View to render the file content?
        $toRender = $this->_hasParam('render') && $this->_getParam('render');
        
        // don't use LAYOUT at all, and don't use any filters
        $this->_helper->layout->disableLayout();
        $this->view->setFilter(null);
        
        // don't render the file, if not required
        if (!$toRender) {
            $this->_helper->viewRenderer->setNoRender();
        }

        // trying to find the file
        foreach ($this->_computePaths($this->_getParam('file')) as $path) {
            if (file_exists($path)) {
                $file = $path;
            }
        }

        // nothing found in the proposed paths?
        if (!isset($file)) {
            $this->getResponse()->setBody('file not found');
            return;
        }

        if ($toRender) {
            $this->view->setScriptPath(pathinfo($file, PATHINFO_DIRNAME));
            $this->_helper->viewRenderer
                ->setViewScriptPathSpec(pathinfo($file, PATHINFO_BASENAME));
        } else {
            // set proper type of content
            if (extension_loaded('fileinfo')) {
                $finfo = new finfo(FILEINFO_MIME);
                if ($finfo) {
                    $this->getResponse()->setHeader('Content-type', $finfo->file($file));
                }
            }
            $this->getResponse()->setHeader('Content-Length', filesize($file));
            $this->getResponse()->setBody(file_get_contents($file));
        }
    }    
    
    /**
     * Compute and return possible paths for the file name
     *
     * @param string Relative file name
     * @return string[]
     */
    protected function _computePaths($name) 
    {
        return array(
            APPLICATION_PATH . '/views/files/' . $name,
            FAZEND_APP_PATH . '/views/files/' . $name,
        );
    }
    
}

