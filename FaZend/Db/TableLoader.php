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
 * @see Zend_Loader_Autoloader_Interface
 */
require_once 'Zend/Loader/Autoloader/Interface.php';

/**
 * Loader of active row classes
 *
 * @see http://framework.zend.com/manual/en/zend.loader.autoloader.html
 * @package Db
 */
class FaZend_Db_TableLoader implements Zend_Loader_Autoloader_Interface
{

    /**
     * Load class
     *
     * @param string Name of the class to create
     * @return FaZend_Db_Table
     * @throws FaZend_Db_TableLoader_InvalidClass
     */
    public function autoload($class)
    {
        // maybe this class already exists?
        if (class_exists($class)) {
            return;
        }

        $name = substr(strrchr($class, '_'), 1);
                          
        /**
         * @see FaZend_Db_ActiveTable
         */                    
        require_once 'FaZend/Db/ActiveTable.php';
        $eval = eval(
            "
            class {$class} extends FaZend_Db_ActiveTable
            { 
                public function __construct(array \$params = array())
                {
                    return parent::__construct(
                        array_merge(
                            array(
                                'name'     => '{$name}',
                                'rowClass' => 'FaZend_Db_Table_ActiveRow_{$name}',
                            ), 
                            \$params
                        )
                    );
                }
                public static function retrieve(\$param = true)
                {
                    return new FaZend_Db_Wrapper('{$name}', \$param);
                }    
            };
            "
        );
        
        if (false === $eval) {
            FaZend_Exception::raise(
                'FaZend_Db_TableLoader_InvalidClass',
                "Class {$class} can't be declared, some error in its definition"
            );
        }
    }

}
