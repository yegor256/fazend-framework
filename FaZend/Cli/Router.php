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
 * Router of CLI (command line interface) calls
 *
 * @see http://code.google.com/p/fazend/wiki/FaZend_Cli
 * @package Cli
 */
class FaZend_Cli_Router
{

    /**
     * Dispatch command line call
     *
     * @return int Exit code
     */
    public function dispatch()
    {
        // strange situation, we should flag it
        if (empty($_SERVER['argc'])) {
            return self::_error('$_SERVER[argc] is not defined, how come?');
        }

        $argc = $_SERVER['argc'];

        // if there are not enough arguments
        if ($argc < 2) {
            return self::_error(
                'FaZend ' . FaZend_Revision::VERSION . ' (' . FaZend_Revision::get() . ") is alive\n" .
                'You started the application from the command line ("php index.php" or something), not from Web. ' .
                'In such a case you should specify a class name, ' . 
                'which has to be located in APPLICATION_PATH/cli and should be ' .
                'an instance of FaZend_Cli_Interface, e.g. "php index.php Backup" ("Backup" is a sample class name).'
            );
        }

        // strange situation, we should flag it
        if (empty($_SERVER['argv'])) {
            return self::_error('$_SERVER[argv] is not defined, how come?');
        }

        $argv = $_SERVER['argv'];

        $cliName = $argv[1];

        return $this->call($cliName);
    }                

    /**
     * Error message to show
     *
     * @param string Message to show to the user
     * @return int CLI error code
     */
    protected static function _error($msg)
    {
        echo 'FaZend_Cli_Router::dispatch() raises error in dispatching: ' . $msg . "\n";
        return FaZend_Cli_Abstract::RETURNCODE_ERROR;
    }

    /**
     * Call one class
     *
     * @param string Name of the CLI class
     * @param array Associative array of options to pass
     * @return int Exit code
     */
    public function call($name, array $options = null)
    {
        if (is_null($options)) {
            $options = self::_getCliOptions();
        }

        $paths = $this->_buildPaths($name);
        foreach ($paths as $p) {
            if (file_exists($p)) {
                $cliPath = $p;
                break;
            }
        }

        // still nothing found?
        if (!isset($cliPath)) {
            return self::_error(
                sprintf(
                    "Script '%s' can't be dispatched, candidates are: %s",
                    $name,
                    implode(', ', $paths)
                )
            );
        }

        // require this class once
        eval('require_once $cliPath;'); // for ZCA
        
        // if the class is not found...
        if (!class_exists($name)) {
            return self::_error("Class '{$name}' is not defined in '{$cliPath}', why?");
        }

        $cli = new $name();

        $cli->setOptions($options);
        $cli->setRouter($this);

        try {
            $result = $cli->execute();
        } catch (FaZend_Cli_OptionMissedException $e) {
            return self::_error($e->getMessage());
        }   
        return $result;
    }
        
    /**
     * Get options from ARGC/ARGV
     *
     * @return array
     */
    protected static function _getCliOptions()
    {
        $argv = $_SERVER['argv'];

        $options = array();
        foreach (array_slice($argv, 2) as $opt) {
            $matches = array();
            if (!preg_match('/^\-\-([\-\w]+)(?:\=(.*?))?$/', $opt, $matches)) {
                return self::_error("Invalid option: '$opt'. Correct format is: '--name=value'");
            }

            $name = strtolower($matches[1]);

            if (isset($matches[2])) {
                $options[$name] = $matches[2];    
            } else {
                $options[$name] = true;    
            }
        }

        return $options;
    }    

    /**
     * Construct a list of absolute file path names
     *
     * @param string Name of the script to run
     * @return string[]
     */
    protected function _buildPaths($name) 
    {
        return array(
            APPLICATION_PATH . '/cli/' . $name . '.php',
            FAZEND_APP_PATH . '/cli/' . $name . '.php',
        );
    }

}
