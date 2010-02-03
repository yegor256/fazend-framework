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

require_once 'phing/Task.php';

/**
 * This is Phing Task for pinging production server
 *
 * @see http://phing.info/docs/guide/current/chapters/ExtendingPhing.html#WritingTasks
 * @package Application
 * @subpackage Phing
 */
class PingFaZend extends Task
{

    /**
     * URL to ping
     *
     * @var string
     */
    protected $_url = false;

    /**
     * Initiator (when the build.xml sees the task)
     * 
     * @return void
     */
    public function init()
    {
    }

    /**
     * Executes
     * 
     * @return void
     * @throws BuildException
     */
    public function main()
    {
        if (!$this->_url) {
            $this->Log("Nothing to ping, live.home is not specified in properties");
            return;
        }

        $this->Log("Pinging {$this->_url}...");

        $curl = curl_init();

        if (!$curl)
            throw new BuildException(curl_error($curl));    

        curl_setopt($curl, CURLOPT_URL, $this->_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        $response = curl_exec($curl);

        if (!$response)
            throw new BuildException(curl_error($curl));    
        
        curl_close($curl);

        $this->Log("Response (" . strlen($response). "bytes): \n{$response}");
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     * @return void
     */
    public function seturl($url)
    {
        $this->_url = $url;
    }
    
}
