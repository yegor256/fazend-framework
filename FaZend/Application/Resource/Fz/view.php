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
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';

/**
 * Resource for VIEW initialization
 *
 * @uses Zend_Application_Resource_Base
 * @package Application
 * @subpackage Resource
 * @see application.ini
 */
class FaZend_Application_Resource_fz_view extends Zend_Application_Resource_ResourceAbstract
{
    
    /**
     * View instance to use
     *
     * @var Zend_View
     * @see init()
     */
    protected $_view;
    
    /**
     * Initializes the resource
     *
     * @return $this
     * @see Zend_Application_Resource_Resource::init()
     */
    public function init() 
    {
        if (isset($this->_view)) {
            return $this->_view;
        }
        
        // make sure it is loaded already
        $this->_bootstrap->bootstrap('layout');

        // layout reconfigure, if necessary
        $layout = Zend_Layout::getMvcInstance();
        if (!file_exists($layout->getViewScriptPath())) {
            $layout->setViewScriptPath(FAZEND_APP_PATH . '/views/layouts');
        }

        // make sure the view already bootstraped
        $this->_bootstrap->bootstrap('view');
        $this->_view = $this->_bootstrap->getResource('view');
        
        $options = $this->getOptions();

        // save View into registry
        Zend_Registry::set('Zend_View', $this->_view);

        // set the type of docs
        $this->_view->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);

        // set proper paths for view helpers and filters
        $this->_view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Helper');
        $this->_view->addHelperPath(FAZEND_PATH . '/View/Helper', 'FaZend_View_Helper');
        $this->_view->addFilterPath(FAZEND_PATH . '/View/Filter', 'FaZend_View_Filter');

        // turn compression ON
        if (!empty($options['htmlCompression'])) {
            $this->_compressed = true;
            $this->_view->addFilter('HtmlCompressor');
            /**
             * @see Fazend_CssController
             */
            require_once FAZEND_APP_PATH . '/controllers/CssController.php';
            Fazend_CssController::setCompression(true);
        }

        // turn compression ON
        if (!empty($options['GoogleAnalytics'])) {
            $this->_view->googleAnalytics = $options['GoogleAnalytics'];
        }

        // view paginator
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginationControl.phtml');

        FaZend_View_Helper_Forma_Field::addPluginDir(
            'FaZend_View_Helper_Forma_Field', 
            FAZEND_PATH . '/View/Helper/Forma'
        );
        
        return $this->_view;
    }
    
}
