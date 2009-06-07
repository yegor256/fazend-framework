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
 * @package FaZend 
 */
class FaZend_Backup {

	/**
	 * Internal list of log messages
	 *
	 * @var Zend_Log
	 */
	protected $_log = array();	

	/**
	 * Setup all necessary variables
	 *
	 * @return void
	 */
	public function __construct() {

	}

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
	 * Get latest run time
	 *
	 * @return time
	 */
	public function getLatestLog() {
		$file = $this->_getSemaphoreFileName();

		if (!file_exists($file))
			return 'no log...';

		return file_get_contents($file);
	}

	/**
	 * Execute backup process
	 *
	 * @var Zend_Config
	 * @throws FaZend_Backuper_Exception
	 */
	public function execute() {

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
	 * Backup db
	 *
	 * @return void
	 */
	protected function _backupDatabase() {

		if (empty($this->_getConfig()->content->db))
	        	return $this->_log("Since [content.db] is empty, we won't backup database");

		// mysqldump
		$file = tempnam(sys_get_temp_dir(), 'fz');
		$config = Zend_Db_Table::getDefaultAdapter()->getConfig();
		$cmd = "\"" . $this->_var('mysqldump').
			"\" -v -u \"{$config['username']}\" --password=\"{$config['password']}\" \"{$config['dbname']}\" --result-file=\"{$file}\" 2>&1";
		$result = shell_exec($cmd);

		if (file_exists($file) && (filesize($file) > 1024))
		        $this->_log($this->_nice($file) . " was created");
		else
		        $this->_log($this->_nice($file) . " creation error: " . $result, true);

		// encrypt the SQL
		$this->_encrypt($file);

		// archive it into .GZ
		$this->_archive($file);

		// unique name of the backup file
		$object = $this->_getConfig()->archive->files->prefix . date('ymd-his') . '.data';

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

		if (empty($this->_getConfig()->content->files))
	        	return $this->_log("Since [content.file] is empty, we won't backup files");

		// all files into .TAR
		$file = tempnam(sys_get_temp_dir(), 'fz');
		$cmd = $this-_var('tar') . " -c --file=\"{$file}\" ";

		foreach($this->_getConfig()->content->files->toArray() as $dir)
			$cmd .= "\"{$dir}\"";

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
       		$cmd = $this->_var('openssl') . " enc -blowfish -pass \"{$password}\" < {$file} > {$fileEnc} 2>&1";
		$result = shell_exec($cmd);

		if (file_exists($fileEnc) && filesize($fileEnc))
		        $this->_log($this->_nice($fileEnc) . " was created");
		else
		        $this->_log($this->_nice($fileEnc) . " creation error: " . $result, true);

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
			$this->	_log("Failed to go to {$this->_getConfig()->ftp->dir}", true);	

		$this->_log("Current directory in FTP: ".ftp_pwd ($ftp));	

		if (!@ftp_put($ftp, $object, $file, FTP_BINARY))	
			$this->_log("Failed to upload " . $this->_nice($file), true);	

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

	        $s3 = new Zend_Service_Amazon_S3($this->_getConfig()->S3->key, $this->_getConfig()->S3->secret);	

	        $bucket = $this->_getConfig()->S3->bucket;

	        if (!$s3->isBucketAvailable($bucket)) {
		        $this->_log("S3 bucket [{$bucket}] was created");
		        $s3->createBucket($bucket);
		}

	        $s3->putFile($bucket . '/' . $object, $file);
	        $this->_log($this->_nice($file) . " was uploaded to Amazon S3");

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
			throw new FaZend_Backup_Exception();

	}

	/**
	 * Semaphore file name
	 *
	 * @return string
	 */
	protected function _getSemaphoreFileName() {

		return sys_get_temp_dir() . '/fz-sem-' . md5(WEBSITE_URL) . '.dat';

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
	 * @return void
	 */
	protected function _setSemaphoreTime($log = 'started...') {

		$file = $this->_getSemaphoreFileName();

		file_put_contents($file, $log);

		$this->_log("Semaphore file $file saved, backup process is started");

	}

	/**
	 * Show nice filename
	 *
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

}
