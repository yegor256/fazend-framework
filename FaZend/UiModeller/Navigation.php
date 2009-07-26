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
 * Navigation map
 *
 * @package UiModeller
 */
class FaZend_UiModeller_Navigation {

    const ANONYMOUS = 'anonymous';
    const DEFAULT_SCRIPT = 'index/index';

    /**
     * Instance of this class
     *
     * @var FaZend_UiModeller_Navigation
     */
    protected static $_instance;

    /**
     * Container
     *
     * @var Zend_Navigation
     */
    protected $_container;

    /**
     * ACL
     *
     * @var Zend_ACL
     */
    protected $_acl;

    /**
     * List of all actors found
     *
     * @var string[]
     */
    protected $_actors = array();

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct() {
        $this->_acl = new Zend_Acl();

        $this->_acl->deny();
        $this->_acl->addRole(new Zend_Acl_Role(self::ANONYMOUS));
    }

    /**
     * Singleton
     *
     * @return FaZend_UiModeller_Navigation
     */
    public function getInstance() {
        if (!isset(self::$_instance))
            self::$_instance = new FaZend_UiModeller_Navigation();
        return self::$_instance;
    }

    /**
     * Populate nagivation containter with pages
     *
     * @param Zend_Navigation
     * @param string Script name, like 'index/settings'
     * @return void
     */
    public function discover($script = null) {

        $this->_discover();

        if (!is_null($script))
            $this->_setActiveScript($script);

        return $this->_container;

    }

    /**
     * Get ACL
     *
     * @return Zend_Acl
     */
    public function getAcl() {
        $this->discover();

        return $this->_acl;
    }

    /**
     * Get list of actors
     *
     * @return string[]
     */
    public function getActors() {
        $this->discover();

        return $this->_actors;
    }

    /**
     * Populate nagivation containter with pages
     *
     * @param Zend_Navigation
     * @return void
     */
    protected function _discover() {

        if (isset($this->_container))
            return $this->_container;

        $this->_container = new Zend_Navigation();

        // full list of controllers
        foreach (glob(APPLICATION_PATH . '/views/scripts/*') as $controller) {

            if (!is_dir($controller))
                continue;

            // only file name
            $controller = pathinfo($controller, PATHINFO_FILENAME);

            // filter out 
            if (preg_match('/^(css|js)$/', $controller))
                continue;

            // create and add new page to the current collection
            $section = new Zend_Navigation_Page_Uri(array(
                'label' => $controller,
                'title' => $controller,
                'class' => 'controller',
            ));

            // list of actors who CAN access this controller
            $actors = array();

            // full list of actions
            foreach (glob(APPLICATION_PATH . '/views/scripts/' . $controller . '/*') as $action) {

                // filter out 
                if (!preg_match('/\.phtml$/', $action))
                    continue;

                $action = pathinfo($action, PATHINFO_FILENAME);
            
                // get the file name, without extension
                $label = $controller . '/' . $action;

                // create and add new page to the current collection
                $page = new Zend_Navigation_Page_Uri(array(
                    'label' => $action,
                    'title' => $label,
                    'uri' => Zend_Registry::getInstance()->view->url(array('action'=>'index', 'id'=>$label), 'ui', true, false),
                    'resource' => $label,
                    'class' => 'action',
                ));

                // get the file
                $content = file_get_contents(APPLICATION_PATH . '/views/scripts/' . $controller . '/' . $action . '.phtml');

                $matches = array();
                if (preg_match_all('/<!--\s?\@actor\s?\([\"\'](.*?)[\'\"]\)/', $content, $matches)) {
                    foreach ($matches[1] as $match) {
                        $actors[] = $this->_allow($match, $label);
                    }
                } else {
                    $actors[] = $this->_allow(self::ANONYMOUS, $label);
                }

                // add this page to the container
                $section->addPage($page);
            
            }

            if (count($actors)) {
                // add all of them to the controller
                foreach ($actors as $actor)
                    $this->_allow($actor, $controller);

                $section->resource = $controller;

                // add this page to the container
                $this->_container->addPage($section);
            }
            
        }

        return $this->_container;

    }

    /**
     * Set active script
     *
     * @param string Script name, like 'index/settings'
     * @return void
     */
    protected function _setActiveScript($script) {

        $this->_container->findOneBy('resource', $script)->active = true;

    }

    /**
     * Allow this actor to this page
     *
     * @param string Name of the actor
     * @param string Script
     * @return string name of actor
     */
    protected function _allow($actor, $script) {

        // create a role if it is absent
        if (!$this->_acl->hasRole($actor)) {
            $this->_acl->addRole(new Zend_Acl_Role($actor));
            $this->_actors[] = $actor;
        }

        // create resource if absent
        if (!$this->_acl->has($script))
            $this->_acl->add(new Zend_Acl_Resource($script));

        // allow access for this actor to this resource
        $this->_acl->allow($actor, $script);

        return $actor;

    }


}
