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
                    file_put_contents($file, $content);

                if ($this->_verbose)
                    echo "{$file} processed\n";
            }
        }
    }

}
