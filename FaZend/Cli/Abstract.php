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

require_once 'FaZend/Cli/Interface.php';

/**
 * Class for a CLI executor
 *
 * @package Cli
 */
abstract class FaZend_Cli_Abstract implements FaZend_Cli_Interface
{

    const RETURNCODE_ERROR = -1;
    const RETURNCODE_OK = 0;

    /**
     * The router
     *
     * @var FaZend_Cli_Router
     */
    protected $_router;

    /**
     * Options
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Save the instance of the router
     *
     * @param FaZend_Cli_Router instance of the router
     * @return this
     */
    public function setRouter(FaZend_Cli_Router $router)
    {
        $this->_router = $router;
    }

    /**
     * Save options
     *
     * @param array List of options to set
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->_options = $options;
    }

    /**
     * Get option value
     *
     * @param string Name of the parameter
     * @param boolean Shall we throw exception if it's not found?
     * @return string
     */
    protected function _get($name, $throwException = true)
    {
        $name = strtolower($name);

        if (!isset($this->_options[$name])) {
            if ($throwException)
                FaZend_Exception::raise('FaZend_Cli_OptionMissedException', "Parameter '$name' is missed");
            else
                return false;    
        }    

        return $this->_options[$name];
    }

    /**
     * Call another CLI class, by name
     *
     * @param string Name of the CLI class
     * @return string
     */
    protected function _callCli($name)
    {
        return $this->_router->call($name);
    }

}
