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

require_once 'phing/Task.php';

/**
* This is Phing Task for pinging production server
*
* @see http://phing.info/docs/guide/current/chapters/ExtendingPhing.html#WritingTasks
*/
class PingFaZend extends Task {

    /**
     * URL to ping
     *
     * @var string
     */
    protected $url = false;

    /**
     * Initiator (when the build.xml sees the task)
     * 
     * @return void
     */
    public function init() {
    }

    /**
     * Executes
     * 
     * @return void
     * @throws BuildException
     */
    public function main() {

        if (!$this->url) {
            $this->Log("Nothing to ping, live.home is not specified in properties");
            return;
        }

        $this->Log("Pinging {$this->url}...");

        $curl = curl_init();

        if (!$curl)
            throw new BuildException(curl_error($curl));    

        curl_setopt ($curl, CURLOPT_URL, $this->url);
        curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($curl, CURLOPT_HEADER, 0);
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
    public function seturl($url) {
        $this->url = $url;
    }
    
}
