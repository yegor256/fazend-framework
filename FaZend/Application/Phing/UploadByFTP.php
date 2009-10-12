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
 * This is Phing Task for uploading all files to FTP
 *
 * @see http://phing.info/docs/guide/current/chapters/ExtendingPhing.html#WritingTasks
 * @package Application
 * @subpackage Phing
 */
class UploadByFTP extends Task {

    // these directories/files won't be uploaded
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
    public function init() {
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setserver($server) {
        $this->_server = $server;
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setusername($userName) {
        $this->_userName = $userName;
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setpassword($password) {
        $this->_password = $password;
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setdestdir($destDir) {
        $this->_destDir = $destDir;
    }

    /**
     * Initalizer
     *
     * @param $fileName string
     */
    public function setsrcdir($srcDir) {
        $this->_srcDir = $srcDir;
    }

    /**
     * Executes
     * 
     * @return void
     */
    public function main() {
        $this->Log("FTP params received\n\tserver: {$this->_server}\n\tlogin: {$this->_userName}\n\tpassword: ".
            preg_replace('/./', '*', $this->_password)."\n\tsrcDir: '{$this->_srcDir}'\n\tdestDir: '{$this->_destDir}'");

        $this->ftp = @ftp_connect($this->_server);
        if ($this->ftp === false)
            throw new BuildException("Failed to connect to ftp ({$this->_server})");    

        $this->Log("Logged in successfully to {$this->_server}");    

        if (@ftp_login($this->ftp, $this->_userName, $this->_password) === false)
            throw new BuildException("Failed to login to ftp ({$this->_server})");    

        $this->Log("Connected successfully to FTP as {$this->_userName}");    

        if (@ftp_pasv($this->ftp, true) === false)
            throw new BuildException("Failed to turn PASV mode ON");    

        if (@ftp_chdir($this->ftp, $this->_destDir) === false)
            throw new BuildException("Failed to go to {$this->_destDir}");    

        $this->Log("Current directory in FTP: ".ftp_pwd($this->ftp));    

        $start = time();
        $currentDir = getcwd();
        $uploaded = $this->_uploadFiles($this->_srcDir);
        chdir($currentDir);

        $this->Log("Uploaded {$uploaded} files, " . sprintf('%0.2f', (time() - $start)/60) . 'mins');    

        if (@ftp_close($this->ftp) === false)
            throw new BuildException("Failed to close connection to ftp ({$this->_server})");    

        $this->Log("Disconnected from FTP");    

        // kill temp file
        if (isset($this->_tempFileName))
            unlink($this->_tempFileName);

    }

    /**
     * Upload files, recursively
     *
     * @param string Path to upload (local path)
     * @return void
     */
    protected function _uploadFiles($path) {

        $dir = scandir($path);
        
        $ftpList = @ftp_nlist($this->ftp, '.');    
        if ($ftpList === false)
            throw new BuildException('Failed to get nlist from FTP at ' . ftp_pwd($this->ftp));    

        // delete obsolete elements from FTP server    
        foreach ($ftpList as $ftpEntry) {
            if (!in_array($ftpEntry, $dir)) {
                if (@ftp_delete($this->ftp, $ftpEntry))
                    continue;

                // maybe it's a directory?
                // I can't delete directories recursively yet...
                //throw new BuildException ("Failed to delete FTP file '$ftpEntry' in ".ftp_pwd ($this->ftp));    
            }    
        }    

        // calculate the amount of uploaded files
        $uploaded = 0;

        foreach ($dir as $entry) {

            // don't upload directories or forbidden files
            if (($entry == '.') || ($entry == '..') || in_array($entry, self::$_forbidden))
                continue;

            $fileName = $path.'/'.$entry;

            if (is_dir($fileName)) {
                // this directory doesn't exist yet on the server, we should create it
                if (@ftp_chdir($this->ftp, $entry) === false) {
                    if (@ftp_mkdir($this->ftp, $entry) === false)
                        throw new BuildException("Failed to create dir '$entry' in ".ftp_pwd($this->ftp));    

                    if (@ftp_chdir($this->ftp, $entry) === false)    
                        throw new BuildException("Failed to chdir to '$entry' in ".ftp_pwd($this->ftp));    
    
                    $this->Log("Created directory $entry");    
                }

                $uploaded += $this->_uploadFiles($fileName);

                if (@ftp_cdup($this->ftp) === false)    
                    throw new BuildException("Failed to cdup from '$entry' in ".ftp_pwd($this->ftp));    
    
            } else {

                // compress the file
                $compressedFile = $this->_compressed($fileName);

                // this file already exists?
                if (in_array($entry, $ftpList)) {
                    $lastModified = @ftp_mdtm($this->ftp, $entry);
                    if ($lastModified === -1)
                        throw new BuildException("Failed to get file modification time from ftp_mdtm('$entry')");    

                    // if the server version is younger than the local - we skip this file    
                    // only if the sizes are similar
                    if ($lastModified > filemtime($fileName)) {

                        $currentSize = @ftp_size($this->ftp, $entry);
                        if ($currentSize === -1)
                            throw new BuildException("Failed to get size from ftp_size('$entry')");    

                        // if the files are of the same size, don't upload again
                        if ($currentSize == filesize($compressedFile))
                            continue;    
                    }

                }    

                if (@ftp_put($this->ftp, $entry, $compressedFile, FTP_BINARY) === false)    
                    throw new BuildException("Failed to upload '$fileName' (".filesize($fileName)." bytes)");    

                $uploaded++;
                $this->Log("Uploaded $fileName (".filesize($fileName)." bytes)");    
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
    private function _compressed($fileName) {
        
        // compress only PHP files
        if (!preg_match('/\.(php|phtml|php5)$/', $fileName))
            return $fileName;

        // create ONE temp file for all compressions
        if (!isset($this->_tempFileName))
            $this->_tempFileName = tempnam(TEMP_PATH, 'zendUploader');

        // compress it with 'PHP -W' option
        file_put_contents($this->_tempFileName, shell_exec("php -w {$fileName}"));    

        // return a NEW file name, which will be uploaded
        return $this->_tempFileName;
    }

}
