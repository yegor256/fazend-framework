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
