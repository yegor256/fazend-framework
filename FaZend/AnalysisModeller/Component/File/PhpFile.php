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
 * Just source code file, in PHP
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
class FaZend_AnalysisModeller_Component_File_PhpFile extends FaZend_AnalysisModeller_Component_File {

    /**
     * Reconfigure class according to reflection information
     *
     * @param Reflector Information about entity
     * @return void
     **/
    public function reflect(Reflector $reflector) {
        assert($reflector instanceof Zend_Reflection_File);
        
        // get the name of the file
        $this->_name = pathinfo($reflector->getFileName(), PATHINFO_BASENAME);
        
        // change parent location
        $doc = $reflector->getDocblock();
         
        $this->_moveTo(FaZend_AnalysisModeller_Component_System::getInstance());
        if (false !== $doc->getTag('category'))
            $this->_moveTo($this->_parent->make('category', trim($doc->getTag('category')->getDescription(), "\r\t\n ")));
            
        if (false !== $doc->getTag('package'))
            $this->_moveTo($this->_parent->make('package', trim($doc->getTag('package')->getDescription(), "\r\t\n ")));

        if (false !== $doc->getTag('subpackage'))
            $this->_moveTo($this->_parent->make('package', trim($doc->getTag('subpackage')->getDescription(), "\r\t\n ")));
            
        // add all daughter classes to this location
        foreach ($reflector->getClasses() as $class)
            $this->_parent->factory('class', null, $class);
    }

}
