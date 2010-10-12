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
     * Formatter
     *
     * @var Zend_Log_Formatter_Abstract
     */
    protected $_formatter;

    /**
     * Class Constructor.
     *
     * @param string Absolute file name
     * @throws FaZend_Log_Writer_File_Exception
     */
    public function __construct($file)
    {
        if (empty($file)) {
            FaZend_Exception::raise(
                'FaZend_Log_Writer_File_Exception',
                "File name is mandatory for the writer"
            );
        }
        $this->_formatter = new Zend_Log_Formatter_Simple();
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
     * @throws FaZend_Log_Writer_File_Exception
     */
    protected function _write($event)
    {
        $line = $this->_formatter->format($event);
        $f = @fopen($this->_file, 'a');
        if ($f === false) {
            FaZend_Exception::raise(
                'FaZend_Log_Writer_File_Exception',
                "Failed to fopen('{$this->_file}', 'a+')"
            );
        }
        if (@fwrite($f, $line) === false) {
            FaZend_Exception::raise(
                'FaZend_Log_Writer_File_Exception',
                "Failed to fwrite('{$this->_file}', '{$line}')"
            );
        }
        if (@fclose($f) === false) {
            FaZend_Exception::raise(
                'FaZend_Log_Writer_File_Exception',
                "Failed to fclose('{$this->_file}')"
            );
        }
    }
    
}
