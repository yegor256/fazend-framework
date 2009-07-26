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
 *
 */
class Fazend_UiController extends FaZend_Controller_Action {

    /**
     * Show the entire map of the system
     *
     * @return void
     */
    public function preDispatch() {
        
        // layout reconfigure to fazend
        $layout = Zend_Layout::getMvcInstance();
        $layout->setViewScriptPath(FAZEND_PATH . '/View/layouts/scripts');
        $layout->setLayout('ui');

    }

    /**
     * Show the entire map of the system
     *
     * @return void
     */
    public function indexAction() {

        if ($this->_hasParam('id'))
            $script = $this->_getParam('id');
        else
            $script = FaZend_UiModeller_Navigation::DEFAULT_SCRIPT;

        // pass it to View
        $this->view->script = $script;

        // build the mockup
        $mockup = new FaZend_UiModeller_Mockup($script);
        $this->view->page = $mockup->html($this->view);

        // get current actor from user session
        $this->view->actor = $actor = $this->_getActor($mockup);

        // search and build the whole MAP of the project
        $this->view->navigation()->setContainer(FaZend_UiModeller_Navigation::getInstance()->discover($script))
            ->setAcl(FaZend_UiModeller_Navigation::getInstance()->getAcl())
            ->setRole($actor);

        $this->view->actors = '<ul>';
        foreach (FaZend_UiModeller_Navigation::getInstance()->getActors() as $a) {

            // this actor is NOT active
            if ($a != $actor)
                $a = '<a href="' . $this->view->url(array('action'=>'actor', 'id'=>$script . ':' . $a), 'ui', true, false) . '">' . $a . '</a>';

            $this->view->actors .= '<li class="pic' . ($a == $actor ? ' picActive' : false) . 
                '"></li><li class="actor' . ($a == $actor ? ' active' : false) . '">' . $a . '</li>';
        }
        $this->view->actors .= '</ul>';

    }

    /**
     * Show one mockup
     *
     * @return void
     */
    public function mockupAction() {

        $mockup = new FaZend_UiModeller_Mockup($this->_getParam('id'));

        $this->_returnPNG($mockup->png());

    }

    /**
     * Change current actor
     *
     * @return void
     */
    public function actorAction() {

        list($script, $actor) = explode(':', $this->_getParam('id'));

        // this script should go to next Action
        $this->_setParam('id', $script);

        // maybe new actor can't access this page?
        if (!FaZend_UiModeller_Navigation::getInstance()->getAcl()->isAllowed($actor, $script)) {

            // we will try to find another script
            $script = false;

            // first we try to find HOME, and then any other page
            $pages = array_merge(
                FaZend_UiModeller_Navigation::getInstance()->discover()->findAllBy('class', 'home'),
                FaZend_UiModeller_Navigation::getInstance()->discover()->findAllBy('type', 'action'));

            // go through the list of pages trying to find one that is allowed for the given actor
            foreach ($pages as $page) {
                if (FaZend_UiModeller_Navigation::getInstance()->getAcl()->isAllowed($actor, $page->resource)) {
                    $script = $page->resource;
                    break;
                }
            }

            if ($script)
                $this->_setParam('id', $script);

        }

        if ($script)
            $this->_setActor($actor);

        $this->_forward('index');

    }

    /**
     * Get current actor, taking into account mockup
     *
     * @param FaZend_UiModeller_Mockup
     * @return string
     */
    protected function _getActor(FaZend_UiModeller_Mockup $mockup) {
        $actor = $this->_getNamespace()->actor;
        if (!$actor)
            $actor = FaZend_UiModeller_Navigation::ANONYMOUS;

        // maybe we should switch the actor?
        $availableActors = $mockup->getActors();
        if (!in_array($actor, $availableActors) && ($actor != FaZend_UiModeller_Navigation::ANONYMOUS)) {

            // maybe the list is empty and we should toggle to anonymous?
            if (!count($availableActors))
                $actor = FaZend_UiModeller_Navigation::ANONYMOUS;
            else
                $actor = $availableActors[array_rand($availableActors)];

            $this->_setActor($actor);

        }

        return $actor;
    }

    /**
     * Set current actor
     *
     * @param string Name of the actor
     * @return void
     */
    protected function _setActor($actor) {
        $this->_getNamespace()->actor = $actor;
    }

    /**
     * Get session namespace
     *
     * @return Zend_Session_Namespace
     */
    protected function _getNamespace() {
        return new Zend_Session_Namespace('ui');
    }

}
