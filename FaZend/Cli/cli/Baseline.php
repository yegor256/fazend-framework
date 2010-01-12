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
 * Class for a baselining of the code
 *
 * @package Cli
 */
class Baseline extends FaZend_Cli_Abstract
{

    /**
     * Executor of a command-line command
     *
     * @return string
     */
    public function execute()
    {
        $email = $this->_get('email');
        $dry = $this->_get('dry-run', false);
        
        $collector = new FaZend_Pan_Baseliner_Collector($email, true);
        $map = $collector->collect(APPLICATION_PATH);

        $validator = new FaZend_Pan_Baseliner_Validator(APPLICATION_PATH, true);
        if (!$validator->validate($map))
            return self::RETURNCODE_ERROR;

        if ($dry)
            $path = 'php://stdout';
        else
            $path = FaZend_Pan_Baseliner_Map::getStorageDir(true) . '/' . $email . '.xml';
        $map->save($path);
        
        echo "\nBaseline XML report saved into: {$path}\nDon't forget to commit it to SVN repository\n";

        return self::RETURNCODE_OK;
    }

}
