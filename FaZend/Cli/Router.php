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
 * Router of CLI (command line interface) calls
 *
 * @see http://code.google.com/p/fazend/wiki/FaZend_Cli
 * @package FaZend 
 */
class FaZend_Cli_Router {

    // to be returned in case of error
    const ERROR_CODE = -1;

    /**
     * Dispatch command line call
     *
     * @return int Exit code
     */
    public function dispatch() {

        // strange situation, we should flag it
        if (empty($_SERVER['argc']))
            return self::_error('$_SERVER[argc] is not defined, how come?');

        $argc = $_SERVER['argc'];

        // if there are not enough arguments
        if ($argc < 2)
            return self::_error('You started the application from the command line ("php index.php" or something), not from Web. ' .
                'In such a case you should specify a class name, which has to be located in APPLICATION_PATH/cli and should be ' .
                'an instance of FaZend_Cli_Interface, e.g. "php index.php Backup" ("Backup" is a sample class name).');

        // strange situation, we should flag it
        if (empty($_SERVER['argv']))
            return self::_error('$_SERVER[argv] is not defined, how come?');

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
    protected static function _error($msg) {

        echo 'FaZend_Cli_Router::dispatch() raises error in dispatching: ' . $msg . "\n";
        return self::ERROR_CODE;

    }

    /**
     * Call one class
     *
     * @param string Name of the CLI class
     * @param array Associative array of options to pass
     * @return int Exit code
     */
    public function call($name, $options = false) {

        if (!$options)
            $options = self::_getCliOptions();

        $cliPath = APPLICATION_PATH . '/cli/' . $name . '.php';

        if (!file_exists($cliPath))
            return self::_error("File '$cliPath' is missed, why?");

        require_once $cliPath;

        if (!class_exists($name))    
            return self::_error("Class '$name' is not defined, why?");

        $cli = new $name();

        $cli->setOptions($options);
        $cli->setRouter($this);

        try {
            return $cli->execute();
        } catch (FaZend_Cli_OptionMissedException $e) {
            return self::_error($e->getMessage());
        }    
    }
        
    /**
     * Get options from ARGC/ARGV
     *
     * @return array
     */
    protected static function _getCliOptions() {

        $argv = $_SERVER['argv'];

        $options = array();
        foreach (array_slice($argv, 2) as $opt) {
            $matches = array();
            if (!preg_match('/^\-\-(\w+)(?:\=(.*?))?$/', $opt, $matches))
                return self::_error("Invalid option: '$opt'. Correct format is: '--name=value'");

            $name = strtolower($matches[1]);

            if (isset($matches[2]))    
                $options[$name] = $matches[2];    
            else    
                $options[$name] = true;    
        }

        return $options;
    }    

}
