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
 * @see FaZend_Backup_Policy_Abstract
 */
require_once 'FaZend/Backup/Policy/Abstract.php';

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
        'key'    => '?',
        'secret' => '?',
        'bucket' => '?',
        'age'    => 168, // in hours, 7 days by default
    );
    
    /**
     * Save files into Amazon S3 bucket.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::forward()
     * @see FaZend_Backup::execute()
     */
    public function forward() 
    {
        $s3 = new Zend_Service_Amazon_S3(
            $this->_options['key'], 
            $this->_options['secret']
        );
        // workaround for this defect: ZF-7990
        // http://framework.zend.com/issues/browse/ZF-7990
        Zend_Service_Amazon_S3::getHttpClient()->setUri('http://google.com');

        $bucket = $this->_config['bucket'];

        if (!$s3->isBucketAvailable($bucket)) {
            $s3->createBucket($bucket);
        }
        
        $cnt = 0;
        foreach (new DirectoryIterator($this->_dir) as $f) {
            if ($f->isDot()) {
                continue;
            }
            $file = $f->getPathname();
            if (is_dir($file)) {
                FaZend_Exception::raise(
                    'FaZend_Backup_Policy_Save_Amazon_Exception',
                    "We can't save directory '{$f}' to Amazon, use Archive first"
                );
            }

            $dest = pathinfo($file, PATHINFO_BASENAME);
            $s3->putFile($file, $bucket . '/' . $dest);
            logg(
                "File '%s' uploaded to AmazonS3 bucket '%s'",
                $file,
                $bucket
            );
            $cnt++;
        }
        if (!$cnt) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Save_Amazon_Exception',
                "No files to send to S3, the directory is empty"
            );
        }
    }
    
    /**
     * Restore files from Amazon S3 bucket into directory.
     *
     * @return void
     * @see FaZend_Backup_Policy_Abstract::backward()
     */
    public function backward() 
    {
        
    }
    
    /**
     * Clear expired files from Amazon.
     *
     * @param Zend_Service_Amazon_S3 Connector to Amazon
     * @return void
     */
    protected function _clean(Zend_Service_Amazon_S3 $s3)
    {
        $bucket = $this->_config['bucket'];
        $files = $s3->getObjectsByBucket($bucket);    
        if (!is_array($files)) {
            FaZend_Exception::raise(
                'FaZend_Backup_Policy_Save_Amazon_Exception',
                "getObjectsByBucket('{$bucket}') failed"
            );
        }

        foreach ($files as $file) {
            $info = $s3->getInfo($bucket . '/' . $file);

            $expired = Zend_Date::now()->sub($this->_options['age'], Zend_Date::HOUR)
                ->isLater($info['mtime']);
            if (!$expired) {
                continue;
            }
            $s3->removeObject($bucket . '/' . $file);
            logg(
                "File '%s' removed from S3, since it's expired (over %d hours)",
                $file,
                $this->_options['age']
            );
        }
    }
    
}
