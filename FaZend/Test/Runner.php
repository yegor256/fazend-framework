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
 * Runner of a single unit test
 *
 * @package FaZend 
 */
class FaZend_Test_Runner {

    /**
     * Unique name of the test
     *
     * @var string
     */
    protected $_name;

    /**
     * Executor
     *
     * @var FaZend_Exec
     */
    protected $_exec;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct($name) {
        
        $this->_name = $name;

        // create Exec (or link to the existing one)
        $this->_exec = FaZend_Exec::create($this->_name);

    }

    /**
     * Run one single test and return XML result
     *
     * phpUnit 3.3.16 is used in time of development
     *
     * @param string Name of the unit test file
     * @return array
     */
    public function run() {
        
        // if it's not running yet - start it now
        $this->_initializeExec();

        // execute it and get the log
        $this->_exec->execute();

        // get result and return it
        return $this->result();

    }

    /**
     * Stop one single test 
     *
     * @param string Name of the unit test file
     * @return void
     */
    public function stop() {
        $this->_exec->stop();
    }    

    /**
     * Get result of the test running, current instant result
     *
     * @return array
     */
    public function result() {

        // fill the resulting array
        $result = array(
            'output' => $this->_exec->getCmd() . "\n" . $this->_exec->output(),
            'tests' => array(),
        );
        // grab all other available results
        $this->_grabResult($result);
                                
        // if it's finished - indicate it
        if (!$this->_exec->isRunning()) {
            $result['finished'] = true;
            $this->_deinitializeExec();
        } else {
            $result['finished'] = false;
        }

        return $result;

    }
        
    /**
     * Initialize exec, first time, before starting
     *
     * @return void
     */
    public function _initializeExec() {

        $this->_exec->bootstrap = tempnam(TEMP_PATH, 'fz');
        $this->_exec->testdox = tempnam(TEMP_PATH, 'fz');
        $this->_exec->metrics = tempnam(TEMP_PATH, 'fz');
        $this->_exec->log = tempnam(TEMP_PATH, 'fz');

        // we pass ENV to the testing environment
        file_put_contents($this->_exec->bootstrap, 
            '<?php define("APPLICATION_ENV", "' . APPLICATION_ENV . '"); define("TESTING_RUNNING", true);');
        
        // phpUnit cmd line
        $cmd =
            'phpunit --verbose --stop-on-failure' . 
            ' -d "include_path=' . ini_get('include_path') . PATH_SEPARATOR . realpath(APPLICATION_PATH . '/../library') . '"' . 
            ' --log-xml ' . escapeshellarg($this->_exec->log) .
            ' --bootstrap ' . escapeshellarg($this->_exec->bootstrap) .
            ' --testdox-text ' . escapeshellarg($this->_exec->testdox) .
            (extension_loaded('xdebug') ? ' --log-metrics ' . escapeshellarg($this->_exec->metrics) : false) .
            ' ' . escapeshellarg('test/' . $this->_name) .
            ' 2>&1';

        // workaround for Mac OS X
        // PATH variable, as well as other variables should be defined
        // in /System/Libraries/LaunchDaemons/org.apache.httpd.plist file
        // using the key LSEnvironment
        // however, this key doesn't work in Leopard 10.5
        // @link http://www.nabble.com/The-mysterious-SHAuthorizationRight-key-td14115115.html
        if (!isset($_ENV['PATH']))
            $cmd = '/usr/local/bin/' . $cmd;

        $this->_exec->setCmd($cmd);
        $this->_exec->setDir(realpath(FaZend_Test_Manager::getInstance()->getLocation() . '/..'));

    }
    
    /**
     * De-Initialize exec
     *
     * @param FaZend_Exec
     * @return void
     */
    public function _deinitializeExec() {

        @unlink($this->_exec->bootstrap);
        @unlink($this->_exec->log);
        @unlink($this->_exec->testdox);
        @unlink($this->_exec->metrics);
        
    }

    /**
     * Grab results
     *
     * @param FaZend_Exec
     * @param array Resulting info for JSON
     * @return array
     */
    public function _grabResult(array &$result) {

        $result['testdox'] = (file_exists($this->_exec->testdox) ? 
            @file_get_contents($this->_exec->testdox) : 'just started (' . $this->_name . ')');

        if (file_exists($this->_exec->log) && file_get_contents($this->_exec->log)) {

            // process XML report from phpUnit
            $xml = simplexml_load_file($this->_exec->log);
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

        }

        // show small report
        if ($this->_exec->getDuration()) {
            $result['spanlog'] = sprintf('%dsec', $this->_exec->getDuration()) . 
                ($this->_exec->getPid() ? ' (' . sprintf('%d', $this->_exec->getPid()) . ')' : false);
            
            // if it's a failure - red it
            if (!isset($result['suite']) || $result['suite']['failures'] || $result['suite']['errors'])
                $result['spanlog'] = '<span style="color: #' . FaZend_Image::BRAND_RED . '">' . $result['spanlog'] . '</span>';
            // if success - green it
            elseif (!$result['suite']['failures'] && !$result['suite']['errors'])
                $result['spanlog'] = '<span style="color: #' . FaZend_Image::BRAND_GREEN . '">' . $result['spanlog'] . '</span>';

        } else {
            $result['spanlog'] = false;
        }

        $result['protocol'] = $result['testdox'];

    }

}
