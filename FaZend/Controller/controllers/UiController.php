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

        $this->view->actor = $actor = $this->_getActor();
        $this->view->script = $script = $this->_getParam('id');

        // search and build the whole MAP of the project
        $this->view->navigation()->setContainer(FaZend_UiModeller_Navigation::getInstance()->discover($script))
            ->setAcl(FaZend_UiModeller_Navigation::getInstance()->getAcl())
            ->setRole($actor);

        $mockup = new FaZend_UiModeller_Mockup($script);
        $this->view->page = $mockup->html($this->view);

        $this->view->actors = '<ul><li>';
        foreach (FaZend_UiModeller_Navigation::getInstance()->getActors() as $a) {

            // this actor is NOT active
            if ($a != $actor)
                $a = '<a href="' . $this->view->url(array('action'=>'actor', 'id'=>$script . ':' . $a), 'ui', true, false) . '">' . $a . '</a>';

            $this->view->actors .= '<li' . ($a == $actor ? ' class="active"' : false) . '>' . $a . '</li>';
        }
        $this->view->actors .= '</li></ul>';

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

        $this->_setActor($actor);

        $this->_setParam('id', $script);

        $this->_forward('index');

    }

    /**
     * Get current actor
     *
     * @return string
     */
    protected function _getActor() {
        $actor = $this->_getNamespace()->actor;
        if (!$actor)
            $actor = FaZend_UiModeller_Navigation::ANONYMOUS;
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
