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

    /**
     * Populate nagivation containter with pages
     *
     * @param Zend_Navigation
     * @param string Script name, like 'index/settings'
     * @return void
     */
    public static function populateNavigation(&$container, $script) {

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
            ));

            // add this page to the container
            $container->addPage($section);
            
            // full list of actions
            foreach (glob(APPLICATION_PATH . '/views/scripts/' . $controller . '/*') as $action) {

                $action = pathinfo($action, PATHINFO_FILENAME);
            
                // get the file name, without extension
                $label = $controller . '/' . $action;

                // create and add new page to the current collection
                $page = new Zend_Navigation_Page_Uri(array(
                    'label' => $action,
                    'title' => $label,
                    'uri' => Zend_Registry::getInstance()->view->url(array('action'=>'index', 'id'=>$label), 'ui', true, false),
                ));

                if ($label == $script)
                    $page->active = true;

                // add this page to the container
                $section->addPage($page);
            
            }

        }

    }

}
