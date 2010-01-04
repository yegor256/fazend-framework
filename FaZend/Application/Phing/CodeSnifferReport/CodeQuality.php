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
 * Quality of file or directory
 *
 * @package Application
 * @subpackage Phing
 */
class CodeQuality {

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
    public function __get($name) {
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
    public function __set($name, $value) {
        $var = '_' . $name;
        $this->$var = $value;
    }
    
    /**
     * Add information from PHPCS file
     *
     * @return void
     **/
    public function setInfo($info) {
        $this->_errors = $info->attributes()->errors;
        $this->_warnings = $info->attributes()->warnings;
    }
    
    /**
     * Merge with child
     *
     * @param CodeQuality Quality of the child file
     * @return void
     **/
    public function merge(CodeQuality $child) {
        $this->_errors += $child->errors;
        $this->_warnings += $child->warnings;
        $this->_lines += $child->lines;
    }
    
    /**
     * Collect information from the file given
     *
     * @return void
     **/
    public function collect($file) {
        $this->_lines = intval(shell_exec('wc -l ' . escapeshellarg($file)));
        
        $info = shell_exec('svn log -l 1 --non-interactive ' . 
            escapeshellarg($file) . ' 2>&1');
        
        // maybe some mistake here
        if (!$info)
            return;
            
        $lines = explode("\n", $info);
        $details = explode('|', $lines[1]);
            
        $this->revision = intval(trim($details[0]));
        $this->author = trim($details[1]);
        $this->log = implode("\n", array_slice($lines, 3, -1));
    }
    
    /**
     * Is it file (TRUE) or directory (FALSE)
     *
     * @return boolean
     **/
    public function isFile() {
        return isset($this->_revision);
    }
    
    /**
     * Calculate quality
     *
     * @return float
     **/
    protected function _getQuality() {
        return round(100 * (1 - ($this->errors + $this->warnings) / $this->lines), 1);
    }
    
}
