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
 * @see FaZend_Application_Resource_Fazend_Email
 */
class FaZend_Email
{
    
    /**
     * Default email
     *
     * @var FaZend_Email
     */
    protected static $_defaultEmail;

    /**
     * View that will render the template
     *
     * @var Zend_View
     * @see setView()
     */
    protected $_view = null;

    /**
     * Mailer that will send this email
     *
     * @var Zend_Mail
     * @see create()
     */
    protected $_mailer = null;

    /**
     * Are we sending emails actually? 
     *
     * @var boolean
     * @see send()
     */
    protected $_isSending = false;
    
    /**
     * Template to use
     *
     * @var string
     * @see setTemplate()
     */
    protected $_template = null;
    
    /**
     * Set default email
     *
     * @param FaZend_Email
     * @return void
     * @see FaZend_Application_Resource_fz_email
     */
    public static function setDefaultEmail(FaZend_Email $email) 
    {
        self::$_defaultEmail = $email;
    }
    
    /**
     * Get default email.
     *
     * @return FaZend_Email
     */
    public static function getDefaultEmail() 
    {
        return self::$_defaultEmail;
    }
    
    /**
     * Set view which will be used to render templates
     *
     * @param Zend_View View to use
     * @return $this
     * @see FaZend_Application_Resource_Fazend_Email::init()
     */
    public function setView(Zend_View $view) 
    {
        $this->_view = clone $view;
        $this->_view->setFilter(null);
        return $this;
    }
    
    /**
     * Set mailer which will be used for sending of emails
     *
     * @param Zend_Mail Mailer
     * @return $this
     * @see FaZend_Application_Resource_Fazend_Email::init()
     */
    public function setMailer(Zend_Mail $mailer) 
    {
        $this->_mailer = $mailer;
        return $this;
    }
    
    /**
     * Set template to render
     *
     * @param string Name of template (relative path)
     * @return $this
     * @see create()
     */
    public function setTemplate($template) 
    {
        $this->_template = $template;
        return $this;
    }
    
    /**
     * Are we sending or not?
     *
     * @param boolean Are we sending or just logging operations?
     * @return $this
     * @see FaZend_Application_Resource_Fazend_Email::init()
     */
    public function setIsSending($isSending) 
    {
        $this->_isSending = $isSending;
        return $this;
    }

    /**
     * Set list of folders where templates are stored
     *
     * @param string[] Folders
     * @return $this
     * @throws FaZend_Email_InvalidFolderException
     * @see FaZend_Application_Resource_Fazend_Email::init()
     */
    public function setFolders(array $folders) 
    {
        foreach ($folders as $dir) {
            if (!file_exists($dir) || !is_dir($dir)) {
                FaZend_Exception::raise(
                    'FaZend_Email_InvalidFolderException', 
                    "Directory '{$dir}' is absent or is not a directory"
                );
            }
        }
        $this->_view->addScriptPath($folders);
        return $this;
    }

    /**
     * Creates an object of class FaZend_Email, statically
     *
     * You should use this method in order to send emails. See the example
     * in {@link __construct()}, for better understanding.
     *
     * @param string Name of the template to use, relative file name (in 'application/emails')
     * @return FaZend_Email
     * @see __construct()
     */
    public static function create($template = null)
    {
        $email = clone self::$_defaultEmail;
        $email->setTemplate($template);
        return $email;
    }

    /**
     * Create a NEW FaZend_Email object, which is empty
     *
     * This method of creating emails is used only in bootstrap, to create
     * the first object. Later you should use factory method {@link create()}
     * in order to clone the next object, using the default one. For example:
     *
     * <code>
     * FaZend_Email::create('account-created.tmpl')
     *     ->set('toEmail', 'john@example.com')
     *     ->set('toName', 'John Smith')
     *     ->set('subject', 'Your account is created!')
     *     ->send();
     * </code>
     *
     * @return void
     * @see FaZend_Application_Resource_Fazend_Email::init()
     */
    public function __construct() 
    {
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
        $this->_view->$key = $value;
        return $this;
    }

    /**
     * Attach content as file
     *
     * @param Zend_Mime_Part To attach
     * @return $this
     */
    public function attach(Zend_Mime_Part $part) 
    {
        $this->_mailer->addAttachment($part);
        return $this;
    }
    
    /**
     * Sends the email
     *
     * @param boolean Send it anyway (no matter what)
     * @return FaZend_Email
     */
    public function send($force = false) 
    {
        $mailer = $this->_fillMailer();
        if ($this->_isSending || $force) {
            $mailer->send();
        } else {
            logg(
                "Email sending skipped:\n" 
                . "\tFrom: " . $mailer->getFrom() . "\n"
                . "\tTo: " . implode('; ', $mailer->getRecipients()) . "\n"
                . "\tSubject: " . $mailer->getSubject() . "\n"
                . "\tMessage (from new line):\n" . $mailer->getBodyText(true)
            );
        }
        return $this;
    }

    /**
     * Log this email to the error log
     *
     * @return FaZend_Email
     */
    public function logError() 
    {
        FaZend_Log::err($this->_mailer->getBodyText()->getContent());
        return $this;
    }

    /**
     * Get filled mailer, with parsed data
     *
     * @return void
     * @throws FaZend_Email_NoTemplateException
     */
    protected function _fillMailer() 
    {
        $mailer = clone $this->_mailer;
        // if the template was specified
        if ($this->_template) {
            try {
                $body = $this->_view->render($this->_template);
            } catch (Zend_View_Exception $e) {
                FaZend_Exception::raise(
                    'FaZend_Email_NoTemplateException', 
                    "Template '{$this->_template}' not found: {$e->getMessage()}"
                );
            }
        
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
                if ($line == '--') {
                    break;
                }
            }    
            $body = trim(implode("\n", array_slice($lines, $id+1)), " \n\r");
        } else {
            $body = $this->_view->body;
        }

        // set body of the email
        $signatureFile = $this->_view->getScriptPath('signature.txt');
        if (file_exists($signatureFile)) {
            $body .= file_get_contents($signatureFile);
        }
            
        $mailer->setBodyText($body);

        // set from user
        $mailer->setFrom(
            $this->_view->fromEmail,
            $this->_view->fromName
        );

        // set recepient
        $mailer->addTo(
            $this->_view->toEmail,
            $this->_view->toName
        );

        // set CC, if required
        if (is_array($this->_view->cc)) {
            foreach ($this->_view->cc as $email=>$name) {
                $mailer->addCc($email, $name);
            }
        }

        // set subject
        $mailer->setSubject($this->_view->subject);
        
        return $mailer;
    }

}
