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

require_once 'Zend/Paginator.php';

/**
 * Paginator
 *
 * @package Paginator 
 */
class FaZend_Paginator extends Zend_Paginator
{

    /**
     * Add paginator to the view
     *
     * @param mixed Holder of data to paginate
     * @param Zend_View View to inject this paginator into
     * @param integer Current page number to set
     * @param string Name of variable to inject into VIEW
     * @return FaZend_Paginator
     */
    public static function addPaginator($iterator, Zend_View $view, $page, $name = 'paginator')
    {
        // if it's an object right after fetchAll(), we should
        // treat is properly and get SELECT from it
        if ($iterator instanceof FaZend_Db_RowsetWrapper) {
            $adapter = new Zend_Paginator_Adapter_DbTableSelect($iterator->select());
        } else {
            // otherwise we think of it as of normal
            // data iterator
            $adapter = new Zend_Paginator_Adapter_Iterator($iterator);
        }

        // we create new paginator
        $paginator = new FaZend_Paginator($adapter);
        
        // configure it
        $paginator->setView($view);
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);

        // and save into View
        return $view->$name = $paginator;
    }

}
