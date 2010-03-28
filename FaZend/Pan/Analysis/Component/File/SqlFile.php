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
 * @version $Id: PhpFile.php 1587 2010-02-07 07:49:26Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * SQL file
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
class FaZend_Pan_Analysis_Component_File_SqlFile extends FaZend_Pan_Analysis_Component_File
{

    /**
     * Reconfigure class according to reflection information
     *
     * @param Reflector Information about entity
     * @return void
     */
    public function reflect(Reflector $reflector)
    {
        assert($reflector instanceof Zend_Reflection_Docblock);
        parent::reflect($reflector);

        $this->_name = preg_replace('/^\d+-/', '', $this->_name);

        $this->_convertTagsToTraces($reflector);

        // change my location
        $this->_relocate($reflector);

        // find all todo tags and add them into $this->_todoTags
        $this->_findTodoTags($reflector);
    }

}
