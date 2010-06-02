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
 * @see Task
 */
require_once 'phing/Task.php';

/**
 * This is Phing Task for uploading all files to FTP
 *
 * @see http://phing.info/docs/guide/current/chapters/ExtendingPhing.html#WritingTasks
 * @package Application
 * @subpackage Phing
 */
class UploadByFTP extends Task
{

    /**
     * These directories/files won't be uploaded
     */
    protected static $_forbidden = array(
        '.svn',
    );

    /**
     * Name of the destination server
     * 
     * @var string
     */
    private $_server;

    /**
     * FTP user name
     * 
     * @var string
     */
    private $_userName;

    /**
     * FTP password
     * 
     * @var string
     */
    private $_password;

    /**
     * Destination director in FTP server
     * 
     * @var string
     */
    private $_destDir;

    /**
     * Local directory with the sources to be uploaded
     * 
     * @var string
     */
    private $_srcDir;

    /**
     * Initiator (when the build.xml sees the task)
     * 
     * @return void
     */
    public function init()
    {
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setserver($server)
    {
        $this->_server = $server;
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setusername($userName)
    {
        $this->_userName = $userName;
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setpassword($password)
    {
        $this->_password = $password;
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setdestdir($destDir)
    {
        $this->_destDir = $destDir;
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setsrcdir($srcDir)
    {
        $this->_srcDir = $srcDir;
    }

    /**
     * Executes
     * 
     * @return void
     */
    public function main()
    {
        $this->Log(
            "FTP params received\n\tserver: {$this->_server}\n\tlogin: {$this->_userName}\n\tpassword: " .
            preg_replace('/./', '*', $this->_password) . 
            "\n\tsrcDir: '{$this->_srcDir}'\n\tdestDir: '{$this->_destDir}'"
        );

        if (!$this->_server) {
            $this->Log("Server is not specified, the deployment won't happen");    
            return;
        }

        $this->ftp = @ftp_connect($this->_server);
        if ($this->ftp === false) {
            $this->_failure("Failed to connect to FTP '{$this->_server}'");    
        }
        $this->Log("Connected successfully to '{$this->_server}'");    

        if (@ftp_login($this->ftp, $this->_userName, $this->_password) === false) {
            $this->_failure("Failed to login to FTP '{$this->_server}'");    
        }
        $this->Log("Logged in successfully to FTP as '{$this->_userName}'");    

        // let's try to play with PASV
        if (@ftp_nlist($this->ftp, '.') === false) {
            $this->_setPassiveMode(true);
            if (@ftp_nlist($this->ftp, '.') === false) {
                $this->_setPassiveMode(false);
                if (@ftp_nlist($this->ftp, '.') === false) {
                    $this->_failure("NLIST doesn't work, neither in normal nor passive mode");    
                }
            }
        }

        if (@ftp_chdir($this->ftp, $this->_destDir) === false) {
            $this->_failure("Failed to go to '{$this->_destDir}'");    
        }
        $this->Log('Current directory in FTP: ' . ftp_pwd($this->ftp));    

        $start = microtime(true);
        $currentDir = getcwd();
        $uploaded = $this->_uploadFiles($this->_srcDir);
        chdir($currentDir);
        $this->Log(
            sprintf(
                'Uploaded %d files, %0.2fmin',
                $uploaded,
                (microtime(true) - $start) / 60
            )
        );    

        if (@ftp_close($this->ftp) === false) {
            $this->_failure("Failed to close connection to FTP '{$this->_server}");    
        }
        $this->Log('Disconnected from FTP');    

        // kill temp file
        if (isset($this->_tempFileName)) {
            unlink($this->_tempFileName);
        }
    }

    /**
     * Upload files, recursively
     *
     * @param string Path to upload (local path)
     * @return void
     */
    protected function _uploadFiles($path)
    {
        $dir = scandir($path);
        
        $ftpList = @ftp_nlist($this->ftp, '.');
        if ($ftpList === false) {
            $this->_failure('Failed to NLIST at ' . @ftp_pwd($this->ftp));    
        }

        // delete obsolete elements from FTP server    
        foreach ($ftpList as $ftpEntry) {
            if (!in_array($ftpEntry, $dir)) {
                if (@ftp_delete($this->ftp, $ftpEntry)) {
                    continue;
                }
                // maybe it's a directory?
                // I can't delete directories recursively yet...
                //$this->_failure ("Failed to delete FTP file '$ftpEntry' in ".ftp_pwd ($this->ftp));    
            }    
        }    

        // calculate the amount of uploaded files
        $uploaded = 0;
        foreach ($dir as $entry) {
            // don't upload directories or forbidden files
            if (($entry == '.') || ($entry == '..') || in_array($entry, self::$_forbidden)) {
                continue;
            }
            $fileName = $path.'/'.$entry;

            if (is_dir($fileName)) {
                // this directory doesn't exist yet on the server, we should create it
                if (@ftp_chdir($this->ftp, $entry) === false) {
                    if (@ftp_mkdir($this->ftp, $entry) === false) {
                        $this->_failure("Failed to MKDIR '{$entry}' in " . ftp_pwd($this->ftp));    
                    }
                    if (@ftp_chdir($this->ftp, $entry) === false) {
                        $this->_failure("Failed to CHDIR to '{$entry}' in " . ftp_pwd($this->ftp));    
                    }
                    $this->Log("Created directory '{$entry}'");    
                }

                $uploaded += $this->_uploadFiles($fileName);

                if (@ftp_cdup($this->ftp) === false) {
                    $this->_failure("Failed to CDUP from '{$entry}' in " . ftp_pwd($this->ftp));    
                }
            } else {
                // compress the file
                $compressedFile = $this->_compressed($fileName);
                // this file already exists?
                if (in_array($entry, $ftpList)) {
                    $lastModified = @ftp_mdtm($this->ftp, $entry);
                    if ($lastModified === -1) {
                        $this->_failure("Failed to get file modification time from ftp_mdtm('{$entry}')");    
                    }

                    // if the server version is younger than the local - we skip this file    
                    // only if the sizes are similar
                    if ($lastModified > filemtime($fileName)) {
                        $currentSize = @ftp_size($this->ftp, $entry);
                        if ($currentSize === -1) {
                            $this->_failure("Failed to get size from ftp_size('{$entry}')");    
                        }

                        // if the files are of the same size, don't upload again
                        if ($currentSize == filesize($compressedFile)) {
                            continue;    
                        }
                    }

                }    

                if (@ftp_put($this->ftp, $entry, $compressedFile, FTP_BINARY) === false) {
                    $this->_failure(
                        sprintf(
                            "Failed to upload '%s' (%d bytes)",
                            $fileName,
                            filesize($fileName)
                        )
                    );    
                }
                $uploaded++;
                $this->Log(
                    sprintf(
                        "Uploaded '%s' (%d bytes)",
                        $fileName,
                        filesize($fileName)
                    )
                );    
            }    
        }
        return $uploaded;
    }

    /**
     * Compress the file and returns the name of compressed file
     *
     * @param $fileName string
     * @return string file name
     */
    protected function _compressed($fileName)
    {
        // compress only PHP files
        if (!preg_match('/\.(php|phtml|php5)$/', $fileName)) {
            return $fileName;
        }

        // create ONE temp file for all compressions
        if (!isset($this->_tempFileName)) {
            $this->_tempFileName = tempnam(TEMP_PATH, 'zendUploader');
        }

        // compress it with 'PHP -W' option
        file_put_contents($this->_tempFileName, shell_exec("php -w {$fileName}"));    

        // return a NEW file name, which will be uploaded
        return $this->_tempFileName;
    }
    
    /**
     * Raise an exception and protocol the failure
     *
     * @return void
     * @throws BuildException
     */
    protected function _failure($text) 
    {
        $this->Log($text);
        throw new BuildException($text);
    }
    
    /**
     * Set passive mode
     *
     * @param boolean
     * @return void
     */
    protected function _setPassiveMode($on = true) 
    {
        if (@ftp_pasv($this->ftp, $on) === false) {
            $this->_failure("Failed to change PASV mode");    
        }
        $this->Log('PASV mode turned ' . ($on ? 'ON' : 'OFF'));    
    }

}
