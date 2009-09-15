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

require_once 'Zend/Loader/Autoloader/Interface.php';

/**
 * Loader of active row classes
 *
 * @see http://framework.zend.com/manual/en/zend.loader.autoloader.html
 */
class FaZend_Db_Table_RowLoader implements Zend_Loader_Autoloader_Interface {

    /**
     * Load class
     *
     * @param string Name of the class to create
     * @return FaZend_Db_Table_Row
     */
    public function autoload ($class) {

        if (class_exists($class))
            return;

        $name = substr(strrchr($class, '_'), 1);

        require_once 'FaZend/Db/Table/ActiveRow.php';
        if (false === eval(
        "class $class extends FaZend_Db_Table_ActiveRow {
            public function __construct(\$id = false) {
                \$this->_table = FaZend_Db_ActiveTable::createTableClass(
                    isset(\$this->_table) ? \$this->_table : '{$name}');
                parent::__construct(\$id);
            }    

            public static function retrieve(\$param = true) {
                \$wrapper = new FaZend_Db_Wrapper('{$name}', \$param);
                return \$wrapper;
            }    
        };"))
            FaZend_Exception::raise('FaZend_Db_Table_InvalidClass',
                "Class $class can't be declared, some error in its definition");

    }

}
