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
 * Facade for code polisher
 *
 * @package Pan
 * @subpackage Polisher
 */
class FaZend_Pan_Polisher_Facade
{
    
    /**
     * Path of source code
     *
     * @var string
     **/
    protected $_path;
    
    /**
     * Dry run? No changes to be made?
     *
     * @var boolean
     **/
    protected $_dry;
    
    /**
     * Echo all results?
     *
     * @var boolean
     **/
    protected $_verbose;

    /**
     * Construct it
     *
     * @param string Path of files
     * @param boolean Dry run, without any actual changes?
     * @param boolean ECHO all results?
     * @return array
     **/
    public function __construct($path, $dry = true, $verbose = true)
    {
        $this->_path = $path;
        $this->_dry = $dry;
        $this->_verbose = $verbose;
    }

    /**
     * Polish and log results
     *
     * @return void
     **/
    public function polish() 
    {
        $fixers = FaZend_Pan_Polisher_Fixer_Abstract::retrieveAll();
        
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->_path)) as $file) {
            if (preg_match('/\/.svn\//', $file))
                continue;
            
            $type = pathinfo($file, PATHINFO_EXTENSION);
            
            $fixed = false;
            $content = file_get_contents($file);
            foreach ($fixers as $fixer) {
                if (!$fixer->isFixable($type))
                    continue;
                try {
                    if ($fixer->fix($content, $type))
                        $fixed = true;
                } catch (FaZend_Pan_Polisher_FixerException $e) {
                    echo "$file: {$e->getMessage()}\n";
                }
            }
            
            if ($fixed) {
                if (!$this->_dry)
                    file_put_contents($file);

                if ($this->_verbose)
                    echo "{$file} processed\n";
            }
        }
    }

}
