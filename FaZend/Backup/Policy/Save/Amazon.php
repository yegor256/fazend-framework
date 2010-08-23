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
 * Save files to Amazon S3.
 *
 * @package Backup
 */
class FaZend_Backup_Policy_Save_Amazon extends FaZend_Backup_Policy_Abstract
{

    /**
     * List of available options.
     *
     * @var array
     */
    protected $_options = array(
    );
    
    /**
     * Get full list of amazon S3 files in the bucket
     *
     * @return array 
     */
    public function getS3Files()
    {
        $s3 = $this->_getS3();

        $bucket = $this->_getConfig()->S3->bucket;

        if (!$s3->isBucketAvailable($bucket)) {
            return array();
        }

        $objects = $s3->getObjectsByBucket($bucket);    

        if (!is_array($objects)) {
            return array();
        }

        return $objects;    
    }

    /**
     * Get info about amazon file
     *
     * @param string Relative file name, in amazon bucket
     * @return array 
     */
    public function getS3FileInfo($file)
    {
        return $this->_getS3()->getInfo($this->_getConfig()->S3->bucket . '/' . $file);    
    }
    
    /**
     * Send this file by FTP
     *
     * @param string File name
     * @return void
     */
    protected function _sendToS3($file, $object)
    {
        if (empty($this->_getConfig()->S3->key) || empty($this->_getConfig()->S3->secret)) {
            $this->_log("Since [S3.key] or [S3.secret] are empty, we won't send files to Amazon S3");
            return;
        }

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
    protected function _cleanS3()
    {
        if (empty($this->_getConfig()->S3->age)) {
            $this->_log("Since [S3.age] is empty we won't remove old files from S3 storage");
            return;
        }

        $bucket = $this->_getConfig()->S3->bucket;

        // this is the minimum time we would accept
        $minTime = time() - $this->_getConfig()->S3->age * 24 * 60 * 60;

        $files = $this->getS3Files();

        foreach ($files as $file) {
            $info = $this->getS3FileInfo($file);

            if ($info['mtime'] < $minTime) {
                $this->_getS3()->removeObject($bucket . '/' . $file);
                $this->_log(
                    "File $file removed from S3, since it's " . 
                    "expired (over {$this->_getConfig()->S3->age} days)"
                );
            }    
        }
    }
    
    /**
     * Get instance of S3 class
     *
     * @return string
     */
    protected function _getS3()
    {
        if (isset($this->_s3)) {
            return $this->_s3;
        }
        
        $this->_s3 = new Zend_Service_Amazon_S3($this->_getConfig()->S3->key, $this->_getConfig()->S3->secret);    
        // workaround for this defect: ZF-7990
        // http://framework.zend.com/issues/browse/ZF-7990
        Zend_Service_Amazon_S3::getHttpClient()->setUri('http://google.com');
        return $this->_s3;
    }

}
