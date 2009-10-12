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

require_once 'FaZend/Cli/Interface.php';

/**
 * Class for a CLI executor
 *
 * @package Cli
 */
abstract class FaZend_Cli_Abstract implements FaZend_Cli_Interface {

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
    public function setRouter(FaZend_Cli_Router $router) {
        $this->_router = $router;
    }

    /**
     * Save options
     *
     * @return this
     */
    public function setOptions(array $options) {
        $this->_options = $options;
    }

    /**
     * Get option value
     *
     * @return this
     */
    protected function _get($name, $throwException = true) {

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
     * @return string
     */
    protected function _callCli($name) {

        return $this->_router->call($name);

    }

}
