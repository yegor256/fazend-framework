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
 * @version $Id: Log.php 1747 2010-03-17 19:17:38Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Send long file by email
 *
 * @package Log
 * @see logg()
 */
class FaZend_Log_Policy_Email extends FaZend_Log_Policy_Abstract
{

    /**
     * List of available options
     *
     * @var array
     */
    protected $_options = array(
        'toEmail'          => null, // to be set in INI file
        'toName'           => 'admin', // to be set in INI file
        'length'           => 50, // in Kb
        'age'              => 5, // days
        'maxLengthToSend'  => 2048, // maximum length of file to be sent by email, in Kb
        'maxContentLength' => 300, // maximum length of file to be sent by email, in Kb
    );

    /**
     * Run the policy
     *
     * @return void
     * @throws FaZend_Log_Policy_Email_Exception
     */
    protected function _run()
    {
        /**
         * If the email is not set
         */
        if (empty($this->_options['toEmail'])) {
            FaZend_Exception::raise(
                'FaZend_Log_Policy_Email_Exception',
                "[toEmail] is not configured in INI"
            );
        }

        /**
         * Here we find all reasons why this file should be sent
         * by email to admin
         */
        $reasons = array();
        if (@filesize($this->_file) > $this->_options['length'] * 1024) {
            $reasons[] = _t(
                'Log file %s is long enough - %dKb (over %dKb)',
                $this->_file,
                filesize($this->_file),
                intval($this->_options['length'])
            );
        }
        if (@filectime($this->_file) > time() - $this->_options['age'] * 24*60*60) {
            $reasons[] = _t(
                'Log file %s is old enough - %ddays (older than %d)',
                $this->_file,
                (@filectime($this->_file) - time()) / (24*60*60),
                intval($this->_options['age'])
            );
        }

        /**
         * If the log file is too big..
         */
        if (@filesize($this->_file) > $this->_options['maxLengthToSend'] * 1024) {
            logg(
                'File %s is too big (%d bytes) to be sent by email (max %dkb allowed)',
                $this->_file,
                @filesize($this->_file),
                $this->_options['maxLengthToSend']
            );
        }

        /**
         * Email the content to the admin
         */
        $sender = FaZend_Email::create('fazendForwardLog.tmpl')
            ->set('toEmail', $this->_options['toEmail'])
            ->set('toName', $this->_options['toName'])
            ->set('file', $this->_file)
            ->set('reasons', $reasons);

        /**
         * If the file is TOO big, we should send it as attachment, not
         * as email body
         */
        $content = @file_get_contents($this->_file);
        if (@filesize($this->_file) > $this->_options['maxContentLength'] * 1024) {
            $sender->set('log', _t('see attached file'));
            $mime = new Zend_Mime_Part($content);
            $mime->filename = pathinfo($this->_file, PATHINFO_BASENAME);
            $sender->attach($mime);
        } else {
            // get the content of the file
            $sender->set('log', $content);
        }
        // send email
        $sender->send();
        
        // truncate the original file
        $this->_truncate($this->_file);
        
        // protocol this operation
        logg(
            'log file was truncated (%dKb) and sent by email to %s',
            strlen($content) / 1024,
            $this->_options['toEmail']
        );
    }

}
