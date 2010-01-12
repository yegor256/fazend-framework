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
 * Interface for a CLI executor
 *
 * @package Cli
 */
interface FaZend_Cli_Interface
{

    /**
     * Executor of a command-line command
     *
     * @return string
     */
    public function execute();

    /**
     * Save options
     *
     * @param array List of options to set
     * @return void
     */
    public function setOptions(array $options);

    /**
     * Save the instance of the router
     *
     * @param FaZend_Cli_Router instance of the router
     * @return void
     */
    public function setRouter(FaZend_Cli_Router $router);
    
}
