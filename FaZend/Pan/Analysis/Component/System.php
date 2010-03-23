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
     * Regex to use for different file types
     *
     * @var string[]
     */
    protected static $_docblockRegexs = array(
        'phtml' => '/^\s*<!--(.*?)-->/s',
        'sql' => '/^\s*((?:--.*\n)+)/',
        'ini' => '/^\s*((?:;;.*\n)+)/',
    );
    
    /**
     * Instance of system
     *
     * @var FaZend_Pan_Analysis_Component_System
     */
    protected static $_instance;
    
    /**
     * Index to expedite search operation
     *
     * @var FaZend_Pan_Analysis_Component_Abstract[]
     * @see findByTrace()
     */
    protected $_searchIndex;

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
     * @param string Full name to look for
     * @return FaZend_Pan_Analysis_Component_Abstract
     * @throws FaZend_Pan_Analysis_Component_System_NotFoundException
     */
    public function findByFullName($name)
    {
        foreach ($this->getIterator() as $item) {
            if ($item->getFullName() == $name) {
                $found = $item;
            }
        }
        if (!isset($found)) {
            FaZend_Exception::raise(
                'FaZend_Pan_Analysis_Component_System_NotFoundException',
                "Component with full name '{$name}' not found"
            );
        }
        return $found;
    }

    /**
     * Initialize class, find ALL components
     *
     * @return void
     * @throws FaZend_Pan_Analysis_Component_System_RootNotFoundException
     */
    protected function _init()
    {
        $dirs = array(
            APPLICATION_PATH
        );
        
        // uncomment this line if you're developing the class
        // $dirs[] = FAZEND_PATH;
            
        foreach ($dirs as $dir) {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir)) as $file) {
                $file = (string)$file;
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                try{
                    switch (true) {
                        case $ext == 'php':
                            // it's necessary for Zend reflection API
                            ob_start();
                            require_once $file;
                            ob_end_clean();
                
                            // try to reflect this file and add it to the collection
                            $this->factory(
                                'file_PhpFile', 
                                substr($file, strlen($dir)+1), 
                                new Zend_Reflection_File($file)
                            );
                            break;
                        
                        case array_key_exists($ext, self::$_docblockRegexs):
                            $matches = array();
                            preg_match(self::$_docblockRegexs[$ext], file_get_contents($file), $matches);
                            $this->factory(
                                'file_' . ucfirst($ext) . 'File',
                                substr($file, strlen($dir)+1),
                                new Zend_Reflection_Docblock(isset($matches[1]) ? $matches[1] : ' ')
                            );
                            break;
                        
                        default:
                            // ignore it...
                    }
                } catch (Zend_Reflection_Exception $e) {
                    FaZend_Log::err(
                        sprintf(
                            "File '%s' ignored. %s: %s",
                            $file,
                            get_class($e),
                            $e->getMessage()
                        )
                    );
                }
            }
        }
        
        // initialize search index...
        $found = $this->findByTrace(self::ROOT);
        if ($found != self::ROOT) {
            FaZend_Exception::raise(
                'FaZend_Pan_Analysis_Component_System_RootNotFoundException',
                "Root not found, internal error: '{$found}'"
            );
        }
    }
        
    /**
     * Find component by some string used in "see tag"
     *
     * @param string Name, vague
     * @param FaZend_Pan_Analysis_Component_Abstract Element to search in
     * @return string|null Component name found, or NULL
     * @see $this->_searchIndex
     * @see getTraces()
     */
    public function findByTrace($name, FaZend_Pan_Analysis_Component_Abstract $parent = null) 
    {
        if (is_null($parent)) {
            $parent = $this;
        }
        
        if (!isset($this->_searchIndex)) {
            $this->_searchIndex = array();
            foreach ($this->getIterator() as $item) {
                $this->_searchIndex[$item->getTraceTag()] = $item->getFullName();
            }
        }
        
        // remove unnecessary chars/words
        $name = preg_replace('/^\$this->|self::/', '', $name);
        
        // here we go through all elements and try to find the most
        // close to the target, using LEVENSHTEIN algorithm. this algorithm
        // compares two strings and returns the number of letters to be
        // replaced/added/deleted from first string to get the second string.
        $best = FaZend_StdObject::create()
            ->set('rate', 0)
            ->set('key', null)
            ->set('base', $name)
            ->set('parent', $parent->getFullName());
        array_walk(
            $this->_searchIndex, 
            create_function(
                '$v, $k, $best', 
                '
                if (strpos($v, $best->parent . FaZend_Pan_Analysis_Component::SEPARATOR) !== 0) {
                    return;
                }
                $rate = levenshtein($k, $best->base);
                if (($rate <= $best->rate) || empty($best->key)) {
                    $best->rate = $rate;
                    $best->key = $k;
                }
                '
            ),
            $best
        );
        
        // if nothing was found and not KEY was set during the search, it means
        // that the tag is not found and NULL shall be returned.
        if (empty($best->key)) {
            return null;
        }
        
        $suffix = preg_quote(substr($name, -5));
        if (!preg_match("/{$suffix}$/", $best->key)) {
            return null;
        }
        
        return $this->_searchIndex[$best->key];
    }

}
