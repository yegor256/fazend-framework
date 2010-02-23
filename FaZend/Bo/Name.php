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
 * @version $Id: Money.php 1587 2010-02-07 07:49:26Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Name of a person
 *
 * Use it like this:
 *
 * <code>
 * $name = new FaZend_Bo_Name('John S. Smith');
 * echo $money->first; // "John"
 * </code>
 *
 * @package Bo
 * @property string $first First name
 * @property string $last Last name
 */
class FaZend_Bo_Name
{
    
    /**
     * List of prefixes, like "Mr.", "Mrs.", "Dr.", etc.
     *
     * @var string
     */
    protected $_prefixes = array();
    
    /**
     * List of suffixes, like "Jr.", "IV", etc.
     *
     * @var string
     * @todo Not implemented yet
     */
    protected $_suffixes = array();
    
    /**
     * Set of names, like array("John", "S.", "Smith")
     *
     * @var string[]
     */
    protected $_names = array();
    
    /**
     * Constructor
     *
     * @param string Personal name
     * @return void
     */
    public function __construct($name)
    {
        $this->set($name);
    }
    
    /**
     * Convert it to string
     *
     * @return string
     */
    public function __toString() 
    {
        return trim(
            implode(' ', $this->_prefixes) . ' ' . 
            implode(' ', $this->_names) . ' ' .
            implode(' ', $this->_suffixes)
        );
    }

    /**
     * Create class
     *
     * @param string Name
     * @return FaZend_Bo_Name
     */
    public static function factory($name)
    {
        return new self($name);
    }

    /**
     * Get certain parts of the class
     *
     * @param string Part to get, property
     * @return string
     */
    public function __get($name) 
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        return false;
    }
    
    /**
     * Set name, parse it
     *
     * @param string Name
     * @return void
     * @throws FaZend_Bo_Name_EmptyException
     */
    public function set($name) 
    {
        // replace all "spaces" by single space-symbols
        $name = trim(preg_replace('/\s+/s', ' ', $name), "\t\r\n ");
        
        // remove all commas
        $name = str_replace(',', '', $name);
        
        // get all parts of the name
        $sectors = explode(' ', $name);
        
        // find and exclude prefixes
        while (current($sectors) && (substr(current($sectors), -1) == '.')) {
            $this->_suffixes[] = current($sectors);
            next($sectors);
        }

        // get names
        while (current($sectors)) {
            $this->_names[] = current($sectors);
            next($sectors);
        }
        
        if (!count($this->_names)) {
            FaZend_Exception::raise(
                'FaZend_Bo_Name_EmptyException', 
                "Invalid name format '{$name}', no valid parts"
            );
        }
    }
    
    /**
     * Get first name
     *
     * @return string
     */
    protected function _getFirst() 
    {
        return $this->_names[0];
    }

    /**
     * Get last name
     *
     * @return string
     */
    protected function _getLast() 
    {
        return $this->_names[count($this->_names) - 1];
    }

}
