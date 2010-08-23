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
     * Execute it.
     *
     * @return integer
     */
    public function execute()
    {
        $protocol = TEMP_PATH . '/' . FaZend_Revision::getName() . '-backup-protocol.txt';
        
        $toRun = false;
        if (!file_exists($protocol)) {
            $toRun = true;
        } else {
            $expired = Zend_Date::now()
                ->sub(FaZend_Backup::getInstance()->getPeriod(), Zend_Date::HOUR)
                ->isLater(filemtime($protocol));
            if ($expired) {
                $toRun = true;
            }
        }
        if ($toRun) {
            $this->_run($protocol);
        }
        echo @file_get_contents($protocol);
        return self::RETURNCODE_OK;
    }
    
    /**
     * Run the backup process and return it's LOG. Put the log
     * of the execution into the file.
     *
     * @param string Absolute name of the protocol file
     * @return string
     */
    protected function _run($protocol) 
    {
        FaZend_Log::getInstance()->addWriter(
            new Zend_Log_Writer_Stream($protocol, 'w'), 
            'fz_backup_writer'
        );
        FaZend_Backup::getInstance()->execute();
        FaZend_Log::getInstance()->removeWriter('fz_backup_writer');
    }

}
