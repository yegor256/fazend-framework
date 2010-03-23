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
 * Just source code file, in PHP
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
class FaZend_Pan_Analysis_Component_File_PhpFile extends FaZend_Pan_Analysis_Component_File
{

    /**
     * Reconfigure class according to reflection information
     *
     * @param Reflector Information about entity
     * @return void
     **/
    public function reflect(Reflector $reflector)
    {
        assert($reflector instanceof Zend_Reflection_File);
        parent::reflect($reflector);
        
        if ($reflector->getDocComment()) {
            $this->_convertTagsToTraces($reflector->getDocblock());
        }

        // change my location
        $this->_relocate($reflector->getDocblock());
        
        // add all daughter classes to this location
        foreach ($reflector->getClasses() as $class) {
            $this->_parent->factory('class', null, $class);
        }

        // find all todo tags and add them into $this->_todoTags
        $this->_findTodoTags($reflector->getDocblock());
    }

}
