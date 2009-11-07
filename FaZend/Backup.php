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
 * Backup database and files and send them to amazon or to FTP server
 *
 * It is executed automatically from PING controller. You should configure
 * its behavior by means of application/backup.ini file. See an example
 * in test-application. 
 *
 * @package Backup
 * @todo Logging procedure refactor for FaZend_Log usage 
 * @todo Zend_Config should be used more effectively, especially for default values
 */
class FaZend_Backup {

    /**
     * Internal list of log messages
     *
     * @var Zend_Log
     */
    protected $_log = array();    

    /**
     * Get full log
     *
     * @return string
     */
    public function getLog() {
        return implode("\n", $this->_log);
    }

    /**
     * Get latest run time
     *
     * @return time
     */
    public function getLatestRunTime() {
        return $this->_getSemaphoreTime();
    }

    /**
     * Remove sempathor
     *
     * @return time
     */
    public function clearSemaphore() {
        unlink($this->_getSemaphoreFileName());
    }

    /**
     * Get latest run time
     *
     * @return time
     */
    public function getLatestLog() {
        $file = $this->_getSemaphoreFileName();

        if (!file_exists($file))
            return 'no log in ' . $file . ' ...';

        return file_get_contents($file);
    }

    /**
     * Execute backup process
     *
     * @var Zend_Config
     * @throws FaZend_Backuper_Exception
     */
    public function execute() {
        $this->_log('Backup started, revision: ' . FaZend_Revision::get());

        try {

            // if backup is not configured
            if (!$this->_getConfig())
                return $this->_log('No configuration found, process is stopped');

            // if backup period is not defined
            if (empty($this->_getConfig()->period))
                return $this->_log('Period is not defined in backup config');

            if ($this->_getSemaphoreTime() > time() - $this->_getConfig()->period * 60 * 60)
                return $this->_log('Latest backup was done less than ' . $this->_getConfig()->period . ' hours ago');
            
            // turn ON the semaphore
            $this->_setSemaphoreTime();
        
            // dump mysql data and produce one ".sql" file
            $this->_backupDatabase();

            // archive all files into one ".tar.gz" file
            $this->_backupFiles();

            // turn ON the semaphore
            $this->_setSemaphoreTime($this->getLog());
        
        } catch (FaZend_Backup_Exception $e) {

            $this->_log("Script terminated by exception");

            unlink($this->_getSemaphoreFileName());

        }
    }

    /**
     * Get full list of amazon S3 files in the bucket
     *
     * @return array 
     */
    public function getS3Files() {
        $s3 = $this->_getS3();

        $bucket = $this->_getConfig()->S3->bucket;

        if (!$s3->isBucketAvailable($bucket))
            return array();

        $objects = $s3->getObjectsByBucket($bucket);    

        if (!is_array($objects))
            return array();

        return $objects;    
    }

    /**
     * Get info about amazon file
     *
     * @param string Relative file name, in amazon bucket
     * @return array 
     */
    public function getS3FileInfo($file) {
        return $this->_getS3()->getInfo($this->_getConfig()->S3->bucket . '/' . $file);    
    }

    /**
     * Backup db
     *
     * @return void
     */
    protected function _backupDatabase() {
        // if we should not backup DB - we skip it
        if (empty($this->_getConfig()->content->db))
            return $this->_log("Since [content.db] is empty, we won't backup database");

        // mysqldump
        $file = tempnam(TEMP_PATH, 'fz');
        $config = Zend_Db_Table::getDefaultAdapter()->getConfig();

        // @see: http://dev.mysql.com/doc/refman/5.1/en/mysqldump.html
        $cmd = $this->_var('mysqldump').
            " -v -u \"{$config['username']}\" --force ".
            "--password=\"{$config['password']}\" \"{$config['dbname']}\" --result-file=\"{$file}\" 2>&1";
        
        $result = shell_exec($cmd);

        if (file_exists($file) && (filesize($file) > 1024))
            $this->_log($this->_nice($file) . " was created with SQL database dump");
        else {
            $this->_log("Command: {$cmd}");
            $this->_log($this->_nice($file) . " creation error: " . $result, true);
        }

        // encrypt the SQL
        $this->_encrypt($file);

        // archive it into .GZ
        $this->_archive($file);

        // unique name of the backup file
        $object = $this->_getConfig()->archive->db->prefix . date('ymd-his') . '.data';

        // send to FTP
        $this->_sendToFTP($file, $object);

        // send to amazon
        $this->_sendToS3($file, $object);

        // kill the file
        unlink($file);
    }

    /**
     * Backup files
     *
     * @return void
     */
    protected function _backupFiles() {
        // if files backup is NOT specified in backup.ini - we skip it
        if (empty($this->_getConfig()->content->files))
            return $this->_log("Since [content.file] is empty, we won't backup files");

        // all files into .TAR
        $file = tempnam(TEMP_PATH, 'fz');
        $cmd = $this->_var('tar') . " -c --file=\"{$file}\" ";

        foreach($this->_getConfig()->content->files->toArray() as $dir)
            $cmd .= "\"{$dir}/*\"";

        $cmd .= " 2>&1";
        $result = shell_exec($cmd);

        // encrypt the .TAR
        $this->_encrypt($file);

        // archive it into .GZ
        $this->_archive($file);

        // unique name of the backup file
        $object = $this->_getConfig()->archive->files->prefix . date('ymd-his') . '.data';

        // send to FTP
        $this->_sendToFTP($file, $object);

        // send to amazon
        $this->_sendToS3($file, $object);
    }

    /**
     * Encrypt one file and change its name
     *
     * @param string File name
     * @return void
     */
    protected function _encrypt(&$file) {
        $fileEnc = $file . '.enc';

        $password = $this->_getConfig()->password;

        $this->_log($this->_nice($file) . " is sent to openssl/blowfish encryption");
               $cmd = $this->_var('openssl') . " enc -blowfish -pass pass:\"{$password}\" < {$file} > {$fileEnc} 2>&1";
        shell_exec($cmd);

        if (file_exists($fileEnc) && (filesize($fileEnc) > 1024))
            $this->_log($this->_nice($fileEnc) . " was created");
        else {
            $this->_log("Command: {$cmd}");
            $this->_log($this->_nice($fileEnc) . " creation error: " . file_get_contents($fileEnc), true);
        }

        $this->_log($this->_nice($file) . " deleted");
        unlink($file);

        $this->_log($this->_nice($fileEnc) . " renamed");
        rename($fileEnc, $file);
    }

    /**
     * Archive one file and change its name to .GZ
     *
     * @param string File name
     * @return void
     */
    protected function _archive(&$file) {
        $cmd = $this->_var('gzip') . " {$file} 2>&1";
        $this->_log($this->_nice($file) . " is sent to gzip");

        $result = shell_exec($cmd);
        $file = $file . '.gz';
        if (file_exists($file) && filesize($file))
            $this->_log($this->_nice($file) . " was created");
        else
            $this->_log($this->_nice($file) . " creation error: " . $result, true);
    }

    /**
     * Send this file by FTP
     *
     * @param string File name
     * @return void
     */
    protected function _sendToFTP($file, $object) {
        // if FTP host is not specified in backup.ini - we skip this method
        if (empty($this->_getConfig()->ftp->host))
            return $this->_log("Since [ftp.host] is empty, we won't send files to FTP");

        $ftp = @ftp_connect($this->_getConfig()->ftp->host);
        if (!$ftp)
            $this->_log("Failed to connect to ftp ({$this->_getConfig()->ftp->host})", true);    

        $this->_log("Logged in successfully to {$this->_getConfig()->ftp->host}");    

        if (!@ftp_login($ftp, $this->_getConfig()->ftp->username, $this->_getConfig()->ftp->password))
            $this->_log("Failed to login to ftp ({$this->_getConfig()->ftp->host})", true);    

        $this->_log("Connected successfully to FTP as {$this->_getConfig()->ftp->username}");    

        if (!@ftp_pasv($ftp, true))
            $this->_log("Failed to turn PASV mode ON", true);    

        if (!@ftp_chdir($ftp, $this->_getConfig()->ftp->dir))
            $this->    _log("Failed to go to {$this->_getConfig()->ftp->dir}", true);    

        $this->_log("Current directory in FTP: ".ftp_pwd ($ftp));    

        if (!@ftp_put($ftp, $object, $file, FTP_BINARY))    
            $this->_log("Failed to upload " . $this->_nice($file), true);    
        else
            $this->_log("Uploaded by FTP: " . $this->_nice($file));    

        if (!@ftp_close($ftp))
            $this->_log("Failed to close connection to ftp ({$this->_getConfig()->ftp->host})");    

        $this->_log("Disconnected from FTP");    

        // remove expired data files
        $this->_cleanFTP();
    }

    /**
     * Clear expired files from FTP
     *
     * @return void
     */
    protected function _cleanFTP() {
        // if FTP file removal is NOT required - we skip this method execution
        if (empty($this->_getConfig()->ftp->age))
            return $this->_log("Since [ftp.age] is empty we won't remove old files from FTP");

        // this is the minimum time we would accept
        $minTime = time() - $this->_getConfig()->ftp->age * 24 * 60 * 60;

        $ftp = @ftp_connect($this->_getConfig()->ftp->host);
        if (!$ftp)
            return $this->_log("Failed to connect to ftp ({$this->_getConfig()->ftp->host})");    

        $this->_log("Logged in successfully to {$this->_getConfig()->ftp->host}");    

        if (!@ftp_login($ftp, $this->_getConfig()->ftp->username, $this->_getConfig()->ftp->password))
            return $this->_log("Failed to login to ftp ({$this->_getConfig()->ftp->host})");    

        $this->_log("Connected successfully to FTP as {$this->_getConfig()->ftp->username}");    

        if (!@ftp_pasv($ftp, true))
            return $this->_log("Failed to turn PASV mode ON");    

        if (!@ftp_chdir($ftp, $this->_getConfig()->ftp->dir))
            return $this->_log("Failed to go to {$this->_getConfig()->ftp->dir}");    

        $this->_log("Current directory in FTP: ".ftp_pwd ($ftp));    

        $files = @ftp_nlist($ftp, '.');    
        if (!$files)
            $this->_log("Failed to get nlist from FTP", true);    

        foreach($files as $file) {
            $lastModified = @ftp_mdtm($ftp, $file);
            if ($lastModified == -1) {
                $this->_log("Failed to get mdtm from '$file'");    
                continue;
            }    

            if ($lastModified < $minTime) {
                if (!@ftp_delete($ftp, $file))
                    $this->_log("Failed to delete file $file");
                else    
                    $this->_log("File $file removed, since it's expired (over {$this->_getConfig()->S3->age} days)");
            }    
        }

        if (!@ftp_close($ftp))
            $this->_log("Failed to close connection to ftp ({$this->_getConfig()->ftp->host})");    

        $this->_log("Disconnected from FTP");    
    }

    /**
     * Send this file by FTP
     *
     * @param string File name
     * @return void
     */
    protected function _sendToS3($file, $object) {
        if (empty($this->_getConfig()->S3->key) || empty($this->_getConfig()->S3->secret))
            return $this->_log("Since [S3.key] or [S3.secret] are empty, we won't send files to Amazon S3");

        $s3 = $this->_getS3();    

        $bucket = $this->_getConfig()->S3->bucket;

        if (!$s3->isBucketAvailable($bucket)) {
            $this->_log("S3 bucket [{$bucket}] was created");
            $s3->createBucket($bucket);
        }

        $s3->putFile($file, $bucket . '/' . $object);
        $this->_log($this->_nice($file) . " was uploaded to Amazon S3");

        // remove expired data files
        $this->_cleanS3();
    }

    /**
     * Clear expired files from amazon
     *
     * @return void
     */
    protected function _cleanS3() {
        if (empty($this->_getConfig()->S3->age))
            return $this->_log("Since [S3.age] is empty we won't remove old files from S3 storage");

        $bucket = $this->_getConfig()->S3->bucket;

        // this is the minimum time we would accept
        $minTime = time() - $this->_getConfig()->S3->age * 24 * 60 * 60;

        $files = $this->getS3Files();

        foreach($files as $file) {
            $info = $this->getS3FileInfo($file);

            if ($info['mtime'] < $minTime) {
                $this->_getS3()->removeObject($bucket . '/' . $file);
                $this->_log("File $file removed from S3, since it's expired (over {$this->_getConfig()->S3->age} days)");
            }    
        }
    }

    /**
     * Get config
     *
     * @return Zend_Config
     */
    protected function _getConfig() {
        if (isset($this->_config))
            return $this->_config;

        $file = APPLICATION_PATH . '/config/backup.ini';
        
        if (!file_exists($file))
            return $this->_log("File $file is absent");
        
        // load config file
        return $this->_config = new Zend_Config_Ini($file, 'backup', true);
    }

    /**
     * Log one message
     *
     * @return void
     * @throws FaZend_Backup_Exception
     */
    protected function _log($message, $throw = false) {
        $this->_log[] = '[' . date('h:i:s') . '] ' . $message;
        if ($throw)
            FaZend_Exception::raise('FaZend_Backup_Exception');
    }

    /**
     * Semaphore file name
     *
     * @return string
     */
    protected function _getSemaphoreFileName() {
        $this->_log('Semaphore file unique name for ' . WEBSITE_URL);
        return TEMP_PATH . '/fz-sem-' . md5(WEBSITE_URL) . '.dat';
    }

    /**
     * When latest backup was done?
     *
     * @return time
     */
    protected function _getSemaphoreTime() {
        $file = $this->_getSemaphoreFileName();

        if (!file_exists($file)) {
            $this->_log("Semaphore file $file is absent, we assume that backup wasn't done yet");
            return false;   
        }    

        $time = filemtime($file);
        $this->_log("Semaphore file $file says that the latest backup was started on " . date('m/d/y h:i:s', $time));
        return $time;
    }

    /**
     * Say that we just started the backup process
     *
     * @param string Log to put into the file
     * @return void
     */
    protected function _setSemaphoreTime($log = 'started...') {
        $file = $this->_getSemaphoreFileName();

        // save content into semaphore file
        file_put_contents($file, $log);

        $this->_log("Semaphore file $file saved (" . strlen($log) . " bytes), backup process is started/finished");
    }

    /**
     * Show nice filename
     *
     * @param string Absolute file name
     * @return string
     */
    protected function _nice($file) {
        if (!file_exists($file))
            return basename($file) . ' (absent)';

        return basename($file) . ' (' . filesize($file). 'bytes)';
    }

    /**
     * Get config or default
     *
     * @return string
     */
    protected function _var($name, $default = false) {
        if (empty($this->_getConfig()->$name)) {

            if (!$default)
                $default = $name;

            $this->_log("Since [{$name}] is empty we use default value: $default");
            return $default;    
        } else {
            return $this->_getConfig()->$name;
        }    
    }

    /**
     * Get instance of S3 class
     *
     * @return string
     */
    protected function _getS3() {
        if (isset($this->_s3))
            return $this->_s3;
        
        $this->_s3 = new Zend_Service_Amazon_S3($this->_getConfig()->S3->key, $this->_getConfig()->S3->secret);    
        // workaround for this defect: ZF-7990
        // http://framework.zend.com/issues/browse/ZF-7990
        Zend_Service_Amazon_S3::getHttpClient()->setUri('http://google.com');
        return $this->_s3;
    }

}
