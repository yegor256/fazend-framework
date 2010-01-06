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

        return self::RETURNCODE_OK;
    }

}
