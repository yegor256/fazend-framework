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
 * Quality of file or directory
 *
 * @package Application
 * @subpackage Phing
 */
class CodeQuality
{

    /**
     * Number of lines
     *
     * @var integer
     */
    protected $_lines = 0;

    /**
     * Number of warnings
     *
     * @var integer
     */
    protected $_warnings = 0;

    /**
     * Number of errors
     *
     * @var integer
     */
    protected $_errors = 0;

    /**
     * Revision number
     *
     * @var integer
     */
    protected $_revision;

    /**
     * Author of the latest commit
     *
     * @var string
     */
    protected $_author;

    /**
     * Latest commit log
     *
     * @var string
     */
    protected $_log;

    /**
     * Get protected variable
     *
     * @param string Name of the variable to get
     * @return mixed
     * @throws Exception
     **/
    public function __get($name)
    {
        $var = '_' . $name;
        if (property_exists($this, $var))
            return $this->$var;
            
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
            
        throw new Exception("What is $name in " . get_class($this));
    }
    
    /**
     * Set protected variable
     *
     * @return void
     **/
    public function __set($name, $value)
    {
        $var = '_' . $name;
        $this->$var = $value;
    }
    
    /**
     * Add information from PHPCS file
     *
     * @return void
     **/
    public function setInfo($info)
    {
        $this->_errors = $info->attributes()->errors;
        $this->_warnings = $info->attributes()->warnings;
    }
    
    /**
     * Merge with child
     *
     * @param CodeQuality Quality of the child file
     * @return void
     **/
    public function merge(CodeQuality $child)
    {
        $this->_errors += $child->errors;
        $this->_warnings += $child->warnings;
        $this->_lines += $child->lines;
    }
    
    /**
     * Collect information from the file given
     *
     * @param string File name
     * @return void
     * @throws Exception
     **/
    public function collect($file)
    {
        $this->_lines = intval(shell_exec('wc -l ' . escapeshellarg($file)));
        
        $cmd = 'svn log --non-interactive ' . escapeshellarg($file) . ' 2>&1';
        $info = shell_exec($cmd);
        
        // maybe some mistake here
        if (!$info)
            throw new Exception("Invalid info from SVN: {$info}, while running: {$cmd}");
            
        $lines = explode("\n", $info);
        if (!preg_match('/^r(\d+)\s?\|\s?(.*?)\s?\|.*?\|\s(\d) line/', $lines[1], $matches))
            throw new Exception("Invalid log line from SVN: {$lines[1]}, while running: {$cmd}");
            
        $this->revision = intval($matches[1]);
        $this->author = $matches[2];
        $this->log = implode("\n", array_slice($lines, 3, intval($matches[3])));
    }
    
    /**
     * Is it file (TRUE) or directory (FALSE)
     *
     * @return boolean
     **/
    public function isFile()
    {
        return isset($this->_revision);
    }
    
    /**
     * Calculate quality
     *
     * @return float
     **/
    protected function _getQuality()
    {
        return round(100 * (1 - ($this->errors + $this->warnings) / $this->lines), 1);
    }
    
}
