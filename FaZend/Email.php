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
 * Send email using template
 *
 * @package Email 
 */
class FaZend_Email
{

    /**
     * Configuration of the sender
     *
     * @var Zend_Config
     */
    protected static $_config;

    /**
     * Set of local variables defined through set()
     *
     * @var array
     */
    protected $_variables;

    /**
     * Mailer
     *
     * @var Zend_Mail
     */
    protected $_mailer;

    /**
     * Saves configuration internally
     *
     * @param Zend_Config Config from .ini file
     * @param Zend_View View to use for rendering
     * @return FaZend_Email
     * @throws FaZend_Email_InvalidTransport
     */
    public static function config(Zend_Config $config, Zend_View $view) 
    {
        // to allow further modifications
        self::$_config = new Zend_Config($config->toArray(), true);
        self::$_config->view = clone $view;
        self::$_config->view->setFilter(null);        
        
        if (!isset(self::$_config->encoding))
            self::$_config->encoding = 'utf-8';
            
        if (isset(self::$_config->transport)) {
            switch (strval(self::$_config->transport->name)) {
                case 'Zend_Mail_Transport_Smtp':
                    Zend_Mail::setDefaultTransport(new Zend_Mail_Transport_Smtp(
                        strval(self::$_config->transport->host),
                        self::$_config->transport->params->toArray()
                    ));
                    break;
                    
                case 'Zend_Mail_Transport_Sendmail':
                    // do nothing, it's default
                    break;
                    
                default:
                    FaZend_Exception::raise(
                        'FaZend_Email_InvalidTransport', 
                        'Transport ' . self::$_config->transport->name . ' is unknown'
                    );
            }
        }
    }

    /**
     * Creates an object of class FaZend_Email, statically
     *
     * @param string Name of the template to use, absolute file name (in 'application/emails')
     * @return FaZend_Email
     */
    public static function create($template = null) 
    {
        return new FaZend_Email($template);
    }

    /**
     * Creates an object of class FaZend_Email 
     *
     * @param string Name of the template to use, absolute file name (in 'application/emails')
     * @return void
     */
    public function __construct($template = false) 
    {
        $this->set('template', $template);

        validate()
            ->true(isset(self::$_config->notifier), "You should define resources.Email.notifier in app.ini (author of notify messages)")
            ->true(isset(self::$_config->notifier->email), "You should define resources.Email.notifier.email in app.ini")
            ->true(isset(self::$_config->notifier->name), "You should define resources.Email.notifier.name in app.ini");

        $this->set('fromEmail', self::$_config->notifier->email);
        $this->set('fromName', self::$_config->notifier->name);

        validate()
            ->true(isset(self::$_config->manager), "You should define resources.Email.manager in app.ini (receiver of system emails)")
            ->true(isset(self::$_config->manager->email), "You should define resources.Email.manager.email in app.ini")
            ->true(isset(self::$_config->manager->name), "You should define resources.Email.manager.name in app.ini");

        $this->set('toEmail', self::$_config->manager->email);
        $this->set('toName', self::$_config->manager->name);
    }

    /**
     * Set local value
     *
     * @param string Name of the property
     * @param string Value
     * @return void
     */
    public function set($key, $value) 
    {
        $this->_variables[$key] = $value;
        return $this;
    }

    /**
     * Get local value
     *
     * @param string Name of the element
     * @return mixed|false
     */
    public function get($key) 
    {
        if (!isset($this->_variables[$key]))
            return false;
        return $this->_variables[$key];
    }

    /**
     * Sends the email
     *
     * @param boolean Send it anyway (no matter what)
     * @return FaZend_Email
     */
    public function send($force = false) 
    {
        $mailer = $this->_getFilledMailer();

        if (self::$_config->send || $force)
            // send it out
            $mailer->send();
        else
            logg(
                "Email sending skipped:\n" 
                . "\tFrom: " . $mailer->getFrom() . "\n"
                . "\tTo: " . implode('; ', $mailer->getRecipients()) . "\n"
                . "\tSubject: " . $mailer->getSubject() . "\n"
                . "\tMessage (from new line):\n" . $mailer->getBodyText(true)
            );

        return $this;
    }

    /**
     * Log this email to the error log
     *
     * @return FaZend_Email
     */
    public function logError() 
    {
        FaZend_Log::err($this->_getFilledMailer()->getBodyText()->getContent());
        return $this;
    }

    /**
     * Get filled mailer, with parsed data
     *
     * @return Zend_Mail
     */
    protected function _getFilledMailer() 
    {
        if (isset($this->_mailer))
            return $this->_mailer;

        // this class will send email
        $mail = $this->_createZendMailer();

        // we render the template by means of View
        $view = self::$_config->view;

        // in this folder all email templates are located
        $view->setScriptPath(self::$_config->folder);
        $template = $this->get('template');

        // if the template was specified
        if ($template) {
            // maybe email template is missed?
            if (!file_exists(self::$_config->folder . '/' . $template)) {
                // maybe we can find in FaZend?
                if (file_exists(FAZEND_PATH . '/Email/emails/' . $template)) {
                    $view->setScriptPath(FAZEND_PATH . '/Email/emails/');
                } else {
                    FaZend_Exception::raise(
                        'FaZend_Email_NoTemplate', 
                        'Template ' . $template . ' is missed in ' . self::$_config->folder
                    );
                }
            }

            // set all variables to View for rendering
            foreach ($this->_variables as $key=>$value)
                $view->assign($key, $value);

            $body = $view->render($template);
        
            // replace old-styled new lines with \n
            $body = preg_replace("/\r\n|\n\r|\r/", "\n", $body);
        
            // parse body for extra variables
            $lines = explode("\n", $body);
            foreach ($lines as $id=>$line) {
                $matches = array();
                // format is simple: "variable: value"    
                if (preg_match('/^([\w\d]+)\:(.*)$/', $line, $matches)) {
                    $this->set($matches[1], $matches[2]);
                }    

                // empty line stops parsing
                if ($line == '--')
                    break;
            }    

            $body = trim(implode("\n", array_slice ($lines, $id+1)), " \n\r");
        } else {
            $body = $this->get('body');
        }

        // set body of the email
        $signatureFile = $view->getScriptPath('signature.txt');
        if (file_exists($signatureFile))
            $body .= file_get_contents($signatureFile);
            
        $mail->setBodyText($body);

        // set from user
        $mail->setFrom($this->get('fromEmail'), $this->get('fromName'));

        // set CC, if required
        if (is_array($this->get('cc'))) {
            foreach ($this->get('cc') as $email=>$name)
                $mail->addCc($email, $name);
        }

        // set subject
        $mail->setSubject($this->get('subject'));

        // set recepient
        $mail->addTo($this->get('toEmail'), $this->get('toName'));

        return $mail;
    }

    /**
     * Creates an instance of class Zend_Mail
     *
     * @return Zend_Mail
     */
    protected function _createZendMailer () 
    {
        if (!isset($this->_mailer))
            $this->_mailer = new Zend_Mail(self::$_config->encoding);

        return $this->_mailer;
    }

}
