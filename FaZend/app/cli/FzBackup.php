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
 * Backup starter.
 *
 * @package Cli
 * @see FaZend_Backup
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
        $protocol = TEMP_PATH . '/' . FaZend_Revision::getName() . '-FzBackup.txt';
        $mutex = TEMP_PATH . '/' . FaZend_Revision::getName() . '-FzBackup-pid.txt';

        $toRun = false;
        if (!file_exists($protocol)) {
            $toRun = 'protocol is absent';
        } else {
            $hours = FaZend_Backup::getInstance()->getOption('period');
            if ($hours < 1) {
                FaZend_Exception::raise(
                    'FzBackup_Exception',
                    "Period should be greater than 1 hour"
                );
            }
            $expired = Zend_Date::now()
                ->sub($hours, Zend_Date::HOUR)
                ->isLater(filemtime($protocol));
            if ($expired) {
                $toRun = "protocol expired (>{$hours}hrs)";
            }
        }

        // run it now!
        if ($toRun) {
            // maybe other process is working now?
            if (file_exists($mutex)) {
                $pid = @file_get_contents($mutex);
                if ($pid === false) {
                    FaZend_Exception::raise(
                        'FzBackup_Exception',
                        "Failed to file_get_contents('{$mutex}')"
                    );
                }
                if (!is_numeric($pid)) {
                    FaZend_Exception::raise(
                        'FzBackup_Exception',
                        "Invalid process number in '{$mutex}': '{$pid}'"
                    );
                }
                $pid = intval($pid);

                // we assume that it's UNIX
                if (file_exists("/proc/{$pid}")) {
                    logg("Still running in process: {$pid}");
                    $toRun = false;
                } else {
                    logg("Zombie in process: {$pid}");
                    if (@unlink($mutex) === false) {
                        FaZend_Exception::raise(
                            'FzBackup_Exception',
                            "Failed to unlink('{$mutex}')"
                        );
                    }
                }
            }

            if (@file_put_contents($mutex, getmypid()) === false) {
                FaZend_Exception::raise(
                    'FzBackup_Exception',
                    "Failed to file_put_contents('{$mutex}')"
                );
            }
            $this->_run($protocol);
            if (@unlink($mutex) === false) {
                FaZend_Exception::raise(
                    'FzBackup_Exception',
                    "Failed to unlink('{$mutex}')"
                );
            }
        }

        $age = Zend_Date::now()->sub(filemtime($protocol))->get(Zend_Date::TIMESTAMP);
        printf(
            "Protocol %s (%d bytes) created %dh:%dm:%ds ago (%s):\n%s",
            $protocol,
            filesize($protocol),
            floor($age / 3600),
            floor($age / 60) % 60,
            $age % 60,
            $toRun ? $toRun : 'no need to re-run',
            @file_get_contents($protocol)
        );
        return self::RETURNCODE_OK;
    }

    /**
     * Run the backup process and return it's LOG. Put the log
     * of the execution into the file.
     *
     * @param string Absolute name of the protocol file
     * @return void
     * @throws FzBackup_Exception
     */
    protected function _run($protocol)
    {
        if (file_exists($protocol)) {
            $age = Zend_Date::now()->sub(filemtime($protocol))->get(Zend_Date::TIMESTAMP);
            $lines = array_slice(file($protocol), -10);
            $messages = array(
                sprintf(
                    'Previous protocol (%d bytes) created %dh:%dm:%ds ago',
                    filesize($protocol),
                    floor($age / 3600),
                    floor($age / 60) % 60,
                    $age % 60
                ),
                sprintf(
                    "Latest lines in the previous protocol:\n%s",
                    implode('', array_map(create_function('$l', 'return "\t> " . $l;'), $lines))
                )
            );
        } else {
            $messages = array(
                "Protocol file '{$protocol}' is absent",
            );
        }
        // make it empty
        if (file_put_contents($protocol, '') === false) {
            FaZend_Exception::raise(
                'FzBackup_Exception',
                "Failed to file_put_contents('{$protocol}')"
            );
        }

        $writerName = 'fz_backup_writer';
        FaZend_Log::getInstance()->addWriter(
            new FaZend_Log_Writer_File($protocol),
            $writerName
        );
        foreach ($messages as $m) {
            logg($m);
        }
        try {
            FaZend_Backup::getInstance()->execute($protocol);
        } catch (Exception $e) {
            FaZend_Log::err(
                sprintf(
                    '%s: %s',
                    get_class($e),
                    $e->getMessage()
                )
            );
        }
        FaZend_Log::getInstance()->removeWriter($writerName);
    }

}
