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
 * @version $Id: Memory.php 1747 2010-03-17 19:17:38Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Logger to file.
 *
 * @package Log
 */
class FaZend_Log_Writer_File extends Zend_Log_Writer_Abstract
{
    
    /**
     * Holds the file name.
     *
     * @var string
     */
    protected $_file = null;

    /**
     * Class Constructor.
     *
     * @param string Absolute file name
     */
    public function __construct($file)
    {
        $this->_file = $file;
    }
    
    /**
     * Create a new instance of Zend_Log_Writer_File.
     * 
     * @param array|Zend_Config $config
     * @return Zend_Log_Writer_File
     */
    static public function factory($config) 
    {
        return new self();
    }

    /**
     * Write a message to the log.
     *
     * @param array Event data
     * @return void
     */
    protected function _write($event)
    {
        $line = $this->_formatter->format($event);
        $f = fopen($this->_file, 'a+');
        fprintf($f, $line . "\n");
        fclose($f);
    }
    
}
