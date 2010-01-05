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
