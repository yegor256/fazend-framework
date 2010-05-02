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
 * @version $Id: Abstract.php 1747 2010-03-17 19:17:38Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * @see FaZend_Cli_Abstract
 */
require_once 'FaZend/Cli/Abstract.php';

/**
 * Backup starter
 *
 * @package Cli
 */
class FzBackup extends FaZend_Cli_Abstract
{

    /**
     * Execute it
     *
     * @return inte
     */
    public function execute()
    {
        $backup = new FaZend_Backup();
        $backup->execute();
        echo $backup->getLog();
        return self::RETURNCODE_OK;
    }

}
