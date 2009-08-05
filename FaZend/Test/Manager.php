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
 * Manager of unit tests
 *
 * @package FaZend 
 */
class FaZend_Test_Manager {

    /**
     * Instance of the class
     *
     * @var FaZend_Test_Manager
     */
    protected static $_instance;

    /**
     * Directory with unit tests
     *
     * @var string
     */
    protected $_location;

    /**
     * Instance of the manager
     *
     * @return FaZend_Test_Manager
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new FaZend_Test_Manager();
        }

        return self::$_instance;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function __construct() {
        $this->_location = realpath(APPLICATION_PATH . '/../../test');
    }

    /**
     * Full list of unit tests
     *
     * @return string[]
     */
    public function getTests() {
        return $this->_getTests();
    }

    /**
     * Run one single test and return XML result
     *
     * phpUnit 3.3.16 is used in time of development
     *
     * @param string Name of the unit test file
     * @return array
     */
    public function run($name) {
        
        // if it's not running yet - start it now
        $exec = $this->_initializeExec($name);

        // execute it and get the log
        $output = $exec->execute();

        // fill the resulting array
        $result = array(
            'output' => $exec->getCmd() . "\n" . $output,
            'tests' => array(),
        );
        // grab all other available results
        $this->_grabResult($exec, $result);
                                
        // if it's finished - indicate it
        if ($result['finished'] == true)
            $this->_deinitializeExec($exec, $result);

        return $result;
        
    }

    /**
     * Stop one single test 
     *
     * @param string Name of the unit test file
     * @return array
     */
    public function stop($name) {

        // if it's not running yet - start it now
        $exec = $this->_initializeExec($name);

        $exec->stop();

        return true;

    }    

    /**
     * Initialize exec
     *
     * @param string Name of the unit test file
     * @return FaZend_Exec
     */
    public function _initializeExec($name) {

        // create Exec (or link to the existing one)
        $exec = FaZend_Exec::create($name);

        $exec->bootstrap = TEMP_PATH . '/fzunits.php';
        $exec->testdox = TEMP_PATH . '/fzunits.testdox';
        $exec->metrics = TEMP_PATH . '/fzunits.metrics';
        $exec->log = TEMP_PATH . '/fzunits.xml';

        // we pass ENV to the testing environment
        file_put_contents($exec->bootstrap, 
            '<?php define("APPLICATION_ENV", "' . APPLICATION_ENV . '"); define("TESTING_RUNNING", true);');
        
        // phpUnit cmd line
        $cmd =
            'phpunit --verbose --stop-on-failure' . 
            ' -d "include_path=' . ini_get('include_path') . PATH_SEPARATOR . realpath(APPLICATION_PATH . '/../library') . '"' . 
            ' --log-xml ' . escapeshellarg($exec->log) .
            ' --bootstrap ' . escapeshellarg($exec->bootstrap) .
            ' --testdox-text ' . escapeshellarg($exec->testdox) .
            (extension_loaded('xdebug') ? ' --log-metrics ' . escapeshellarg($exec->metrics) : false) .
            ' ' . escapeshellarg('test/' . $exec->getName());

        $exec->setCmd($cmd);
        $exec->setDir(realpath($this->_location . '/..'));

        return $exec;

    }
    
    /**
     * De-Initialize exec
     *
     * @param FaZend_Exec
     * @param array Resulting info for JSON
     * @return void
     */
    public function _deinitializeExec(FaZend_Exec $exec, array &$result) {

        @unlink($exec->bootstrap);
        @unlink($exec->log);
        @unlink($exec->testdox);
        @unlink($exec->metrics);
        
    }

    /**
     * Grab results
     *
     * @param FaZend_Exec
     * @param array Resulting info for JSON
     * @return array
     */
    public function _grabResult(FaZend_Exec $exec, array &$result) {

        $result['testdox'] = file_exists($exec->testdox) ? file_get_contents($exec->testdox) : 'started...';

        $result['finished'] = false;

        if (file_exists($exec->log) && file_get_contents($exec->log)) {

            // process XML report from phpUnit
            $xml = simplexml_load_file($exec->log);
            foreach ($xml->testsuite->children() as $tc) {
                $result['tests'][] = array(
                    'name' => (string)$tc->attributes()->name,
                    'time' => (float)$tc->attributes()->time,
                    'assertions' => (int)$tc->attributes()->assertions,
                );
            }
            $result['suite'] = array(
                'name' => (string)$xml->testsuite->attributes()->name,
                'tests' => (int)$xml->testsuite->attributes()->tests,
                'assertions' => (int)$xml->testsuite->attributes()->assertions,
                'failures' => (int)$xml->testsuite->attributes()->failures,
                'errors' => (int)$xml->testsuite->attributes()->errors,
                'time' => (float)$xml->testsuite->attributes()->time,
            );

            $result['finished'] = true;

        }

        // show small report
        $result['spanlog'] = sprintf('%dsec', $exec->getDuration()) . 
            ($exec->getPid() ? ' (' . sprintf('%d', $exec->getPid()) . ')' : false);

        // if it's a failure - red it
        if (!isset($result['suite']) || $result['suite']['failures'] || $result['suite']['errors'])
            $result['spanlog'] = '<span style="color: #' . FaZend_Image::BRAND_RED . '">' . $result['spanlog'] . '</span>';

        $result['protocol'] = $result['testdox'];

    }

    /**
     * Get full list of unit tests, recursively called
     *
     * @param string Directory name, after $this->_location
     * @return string[]
     */
    public function _getTests($path = '.') {

        $result = array();
        foreach (glob($this->_location . '/' . $path . '/*') as $file) {

            $matches = array();
            $filePath = $path . '/' . basename($file);

            if (is_dir($file))
                $result = array_merge($result, $this->_getTests($filePath));
            elseif (preg_match('/\.\/(.*?Test).php$/', $filePath, $matches))
                $result[] = $matches[1];
        }

        // reverse sort in order to put directories on top
        rsort($result);

        // return the list of files, recursively
        return $result;

    }

}
