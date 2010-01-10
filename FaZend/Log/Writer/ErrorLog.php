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
 * Error log writer processor
 *
 * Writes messages to error_log and send this file to admin, if it's
 * too long or too old.
 *
 * @package Log
 */
class FaZend_Log_Writer_ErrorLog extends Zend_Log_Writer_Stream
{

    const MAX_LENGTH = 20000; // maximum length of the log file, in bytes
    const MAX_AGE_DAYS = 5; // maximum age of the error_log file in days

    /**
     * Constructs the writer
     *
     * You should define error_log in your php.ini file, or in app.ini
     * give this line:
     *
     * <code>
     * phpSettings.error_log = APPLICATION_PATH "/../../my.log"
     * </code>
     *
     * @return void
     * @throws FaZend_Log_Writer_ErrorLog_NoLogFile
     */
    public function __construct()
    {
        // we try to get the file name from php.ini
        $stream = ini_get('error_log');

        // if it wasn't set...
        if (!$stream) {
            // and if it's a production mode - we should signal
            if (APPLICATION_ENV === 'production')
                FaZend_Exception::raise(
                    'FaZend_Log_Writer_ErrorLog_NoLogFile',
                    '[error_log] is not set in php.ini or in app.ini'
                );
            else
                // otherwise drop the output to stdout
                $stream = 'php://stdout';
        }

        // if we can't write to project log file, let's write to syslog
        if (is_file($stream) && !is_writable($stream))
            $stream = 'php://temp';

        // call parent constructor
        parent::__construct($stream);

        // remove extra file content and send it by email to the site
        // administrator
        try {
            $this->_cutFile($stream);
        } catch (Exception $e) {
            // ignore any exceptions at this stage
            $file = @fopen($stream, 'a+');
            if ($file !== false) {
                @fwrite($file, get_class($e) . ' in ' . get_class($this) . '::__construct(): ' . $e->getMessage() . "\n");
                @fclose($file);
            }
        }
    }

    /**
     * Cut the log file, if necessary
     *
     * @param string File name to cut
     * @return void
     */
    protected function _cutFile($file)
    {
        if (APPLICATION_ENV !== 'production')
            return;

        // if it's not a regular file - skip the process
        if (!@is_file($file))
            return;

        // if it's still small, skip the rest
        // and if it's still very small
        if ((@filesize($file) < self::MAX_LENGTH) && (@filectime($file) > time() - self::MAX_AGE_DAYS * 24 * 60 * 60))
            return;

        // if the file is not writable - skip the process
        if (!@is_writable($file))
            return;

        // if not email configured - skip it
        $email = FaZend_Properties::get()->errors->email;
        if (!$email)
            return;

        // get the content of the file
        $content = @file_get_contents($file);

        // email the content to the admin
        FaZend_Email::create('fazendForwardLog.tmpl')
            ->set('toEmail', $email)
            ->set('toName', 'System Administrator')
            ->set('log', $content)
            ->set('file', $file)
            ->set('maximum', self::MAX_LENGTH)
            ->send();

        // refresh the file
        $handle = @fopen($file, 'w');
        if ($handle === false)
            return;
        if (@ftruncate($handle, 0) === false)
            return;
        @fwrite(
            $handle, 
            date('m/d/Y h:i') . ": file content (" . strlen($content) .
            " bytes) was sent by email ({$email}) to admin.\n\n"
        );
        @fclose($handle);
    }

}
