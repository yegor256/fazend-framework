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

require_once 'FaZend/Cli/Abstract.php';

/**
 * Polish entire source code
 *
 * @package Cli
 */
class Polish extends FaZend_Cli_Abstract
{

    /**
     * Executor of a command-line command
     *
     * @return string
     */
    public function execute()
    {
        $dry = (bool)$this->_get('dry-run', false);
        $path = $this->_get('dir', false);
        if (!$path)
            $path = APPLICATION_PATH;

        // application files
        $polisher = new FaZend_Pan_Polisher_Facade($path, $dry, true);
        $polisher->polish();

        return self::RETURNCODE_OK;
    }

}
