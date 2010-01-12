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
 * Get CLI access to Pan-s
 *
 * @package Cli
 */
class Pan extends FaZend_Cli_Abstract
{

    /**
     * Executor of a command-line command
     *
     * @return string
     */
    public function execute()
    {
        $pan = strtolower($this->_get('pan'));

        switch ($pan) {
            case 'analysis':
                $facade = new FaZend_Pan_Analysis_Facade();
                $list = $facade->getComponentsList();
                echo Zend_Json::encode($list);
                break;
                
            default:
                echo "Pan $pan is not accessible\n";
                return self::RETURNCODE_OK;
        }

        return self::RETURNCODE_OK;
    }

}
