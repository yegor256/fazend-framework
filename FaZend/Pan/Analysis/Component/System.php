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
 * System component, central
 *
 * @package AnalysisModeller
 * @subpackage Component
 */
class FaZend_Pan_Analysis_Component_System extends FaZend_Pan_Analysis_Component_Package
{

    const ROOT = 'System';
    
    /**
     * Instance of system
     *
     * @var FaZend_Pan_Analysis_Component_System
     */
    protected static $_instance;

    /**
     * Get an instance of this class
     *
     * @return FaZend_Pan_Analysis_Component_System
     */
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self(null, self::ROOT);
            self::$_instance->_init();
        }
        return self::$_instance;
    }

    /**
     * Find component by full name
     *
     * @return FaZend_Pan_Analysis_Component_Abstract
     **/
    public function findByFullName($name)
    {
        if ($name == self::ROOT)
            return $this;
            
        $exp = explode(FaZend_Pan_Analysis_Component::SEPARATOR, $name);
        $target = array_pop($exp);
        
        // build parent name
        $parent = implode(FaZend_Pan_Analysis_Component::SEPARATOR, $exp);
        
        return $this->findByFullName($parent)->find($target);
    }

    /**
     * Initialize class, find ALL components
     *
     * @return void
     **/
    protected function _init()
    {
        $dirs = array(
            APPLICATION_PATH
        );
        
        // uncomment this line if you're developing the class
        // $dirs[] = FAZEND_PATH;
            
        foreach ($dirs as $dir) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
                if (!preg_match('/\.php$/i', $file->getFilename()))
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
