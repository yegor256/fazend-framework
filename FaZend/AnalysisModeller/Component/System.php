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
 * System component, central
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
class FaZend_AnalysisModeller_Component_System extends FaZend_AnalysisModeller_Component_Package {

    const ROOT = 'System';
    
    /**
     * Instance of system
     *
     * @var FaZend_AnalysisModeller_Component_System
     */
    protected static $_instance;

    /**
     * Get an instance of this class
     *
     * @return FaZend_AnalysisModeller_Component_System
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self(null, self::ROOT);
            self::$_instance->_init();
        }
        return self::$_instance;
    }

    /**
     * Find component by full name
     *
     * @return FaZend_AnalysisModel_Component_Abstract
     **/
    public function findByFullName($name) {
        if ($name == self::ROOT)
            return $this;
            
        $exp = explode(FaZend_AnalysisModeller_Component::SEPARATOR, $name);
        $target = array_pop($exp);
        
        // build parent name
        $parent = implode(FaZend_AnalysisModeller_Component::SEPARATOR, $exp);
        
        return $this->findByFullName($parent)->find($target);
    }

    /**
     * Initialize class, find ALL components
     *
     * @return void
     **/
    protected function _init() {
        
        $dirs = array(
            APPLICATION_PATH,
            FAZEND_PATH);
            
        foreach ($dirs as $dir) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
                if (!preg_match('/\.php$/', $file->getFilename()))
                    continue;
                
                // it's necessary for Zend reflection API
                ob_start();
                require_once $file;
                ob_end_clean();
                
                // try to reflect this file and add it to the collection
                try {
                    $this->factory('file_PhpFile', (string)$file, new Zend_Reflection_File((string)$file));
                } catch (Zend_Reflection_Exception $e) {
                    FaZend_Log::err("File '{$file} ignored: {$e->getMessage()}");
                }
            }
        }
    }
        
}
