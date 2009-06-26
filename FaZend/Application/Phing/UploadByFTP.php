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
*/
class UploadByFTP extends Task {

    const NO_LATER_THAN = '5/2/2009';

    // these directories/files won't be uploaded
    private static $forbidden = array (
        '.svn',
    );

    private $server;
    private $userName;
    private $password;
    private $destDir;
    private $srcDir;

    /**
    * Initiator (when the build.xml sees the task)
    * 
    * @return void
    */
    public function init () {
    }

    /**
    * Executes
    * 
    * @return void
    */
    public function main () {
        $this->Log ("FTP params received\n\tserver: {$this->server}\n\tlogin: {$this->userName}\n\tpassword: ".
            preg_replace ('/./', '*', $this->password)."\n\tsrcDir: '{$this->srcDir}'\n\tdestDir: '{$this->destDir}'");

        $this->ftp = @ftp_connect ($this->server);
        if (!$this->ftp)
            throw new BuildException ("Failed to connect to ftp ({$this->server})");    

        $this->Log ("Logged in successfully to {$this->server}");    

        if (!@ftp_login ($this->ftp, $this->userName, $this->password))
            throw new BuildException ("Failed to login to ftp ({$this->server})");    

        $this->Log ("Connected successfully to FTP as {$this->userName}");    

        if (!@ftp_pasv ($this->ftp, true))
            throw new BuildException ("Failed to turn PASV mode ON");    

        if (!@ftp_chdir ($this->ftp, $this->destDir))
            throw new BuildException ("Failed to go to {$this->destDir}");    

        $this->Log ("Current directory in FTP: ".ftp_pwd ($this->ftp));    

        $this->filesUploaded = 0;
        $this->uploadFiles ($this->srcDir);
        $this->Log ("Uploaded {$this->filesUploaded} files");    

        if (!@ftp_close ($this->ftp))
            throw new BuildException ("Failed to close connection to ftp ({$this->server})");    

        $this->Log ("Disconnected from FTP");    
    }

    /**
    * Upload files, recursively
    *
    * @param string
    * @return void
    */
    public function uploadFiles ($path) {

        $dir = scandir ($path);
        
        $ftpList = @ftp_nlist ($this->ftp, '.');    
        if (!$ftpList)
            throw new BuildException ("Failed to get nlist from FTP: '$entry'");    

        // delete obsolete elements from FTP server    
        foreach ($ftpList as $ftpEntry) {
            if (!in_array ($ftpEntry, $dir)) {
                if (@ftp_delete ($this->ftp, $ftpEntry))
                    continue;

                // maybe it's a directory?
                // I can't delete directories recursively yet...
                //throw new BuildException ("Failed to delete FTP file '$ftpEntry' in ".ftp_pwd ($this->ftp));    
            }    
        }    

        foreach ($dir as $entry) {

            if (($entry == '.') || ($entry == '..') || in_array ($entry, self::$forbidden))
                continue;

            $fileName = $path.'/'.$entry;

            if (is_dir ($fileName)) {
                // this directory doesn't exist yet on the server, we should create it
                if (!@ftp_chdir ($this->ftp, $entry)) {
                    if (!@ftp_mkdir ($this->ftp, $entry))
                        throw new BuildException ("Failed to create dir '$entry' in ".ftp_pwd ($this->ftp));    

                    if (!@ftp_chdir ($this->ftp, $entry))    
                        throw new BuildException ("Failed to chdir to '$entry' in ".ftp_pwd ($this->ftp));    
    
                    $this->Log ("Created directory $entry");    
                }

                $this->uploadFiles ($fileName);

                if (!@ftp_cdup ($this->ftp))    
                    throw new BuildException ("Failed to cdup from '$entry' in ".ftp_pwd ($this->ftp));    
    
            } else {

                // this file already exists?
                if (in_array ($entry, $ftpList)) {
                    $lastModified = @ftp_mdtm ($this->ftp, $entry);
                    if ($lastModified == -1)
                        throw new BuildException ("Failed to get mdtm from '$entry'");    

                    // if the server version is younger than the local - we skip this file    
                    if (($lastModified > filemtime($fileName)) && ($lastModified > strtotime(self::NO_LATER_THAN)))
                        continue;    
                }    

                if (!@ftp_put ($this->ftp, $entry, $this->_compressed($fileName), FTP_BINARY))    
                    throw new BuildException ("Failed to upload '$fileName' (".filesize ($fileName)." bytes)");    

                $this->filesUploaded++;
                $this->Log ("Uploaded $fileName (".filesize ($fileName)." bytes)");    
            }    

        }

    }

    /**
    * Initalizer
    *
    * @param $fileName string
    */
    public function setserver ($server) {
        $this->server = $server;
    }

    /**
    * Initalizer
    *
    * @param $fileName string
    */
    public function setusername ($userName) {
        $this->userName = $userName;
    }

    /**
    * Initalizer
    *
    * @param $fileName string
    */
    public function setpassword ($password) {
        $this->password = $password;
    }

    /**
    * Initalizer
    *
    * @param $fileName string
    */
    public function setdestdir ($destDir) {
        $this->destDir = $destDir;
    }

    /**
    * Initalizer
    *
    * @param $fileName string
    */
    public function setsrcdir ($srcDir) {
        $this->srcDir = $srcDir;
    }

    /**
    * Compress the file and returns the name of compressed file
    *
    * @param $fileName string
    * @return string file name
    */
    private function _compressed($fileName) {
        
        if (!preg_match('/\.(php|phtml|php5)$/', $fileName))
            return $fileName;

        if (!isset($this->_tempFileName))
            $this->_tempFileName = tempnam(sys_get_temp_dir(), 'zendUploader');

        file_put_contents($this->_tempFileName, shell_exec("php -w {$fileName}"));    

        return $this->_tempFileName;
    }

}
