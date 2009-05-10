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
 * Send email using template
 *
 * @package FaZend 
 */
class FaZend_Email {

	/**
	 * Configuration of the sender
	 *
	 * @var Zend_Config
	 */
	private static $_config;

	/**
	 * Set of local variables defined through set()
	 *
	 * @var array
	 */
	private $_variables;

        /**
         * Creates an object of class Model_Email, statically
         *
         * @return FaZend_Email
         */
	public static function config (Zend_Config $config) {
		$this->_config = $config;
	}

        /**
         * Creates an object of class Model_Email, statically
         *
         * @return FaZend_Email
         */
	public static function create ($template) {
		return new FaZend_Email($template);
	}

        /**
         * Creates an object of class Model_Email 
         *
         * @return void
         */
	public function __construct ($template = false) {
		$this->set('template', $template);

		$this->set('fromEmail', $this->_config->notifier->email);
		$this->set('fromName', $this->_config->email->notifier->name);

		$this->set('toEmail', $this->_config->email->manager->email);
		$this->set('toName', $this->_config->email->manager->name);
	}

        /**
         * Set local value
         *
         * @return void
         */
	public function set ($key, $value) {
		$this->_variables[$key] = $value;
		return $this;
	}

        /**
         * Get local value
         *
         * @return var
         */
	public function get ($key) {
		return $this->_variables[$key];
	}

        /**
         * Sends the email
         *
         * @return void
         */
	public function send () {
	        // this class will send email
		$mail = $this->_createZendMailer();

		// we render the template by means of View
		$view = new Zend_View();

		// in this folder all email templates are located
		$view->setScriptPath($this->_config->folder);

		// set all variables to View for rendering
		foreach ($this->_variables as $key=>$value)
			$view->assign($key, $value);

		// render the body and kill all \r signs	
		$body = str_replace ("\r", '', $view->render($this->get('template')));	

		// parse body for extra variables
		$lines = explode ("\n", $body);
		foreach ($lines as $id=>$line) {
			// format is simple: "variable: value"	
			if (strpos ($line, ':')) {
				list($key, $value) = explode (':', $line);	
				$value = trim($value);
				$key = trim($key);

				$this->set($key, $value);
			}	

			// empty line stops parsing
			if ($line == '--')
				break;

		}	

		$body = implode("\n", array_slice ($lines, $id+1));

		// set body of the email
		$mail->setBodyText(trim ($body, " \n\r").file_get_contents ($view->getScriptPath('signature.txt')));

		// set from user
		$mail->setFrom($this->get('fromEmail'), $this->get('fromName'));

		// set subject
		$mail->setSubject($this->get('subject'));

		// set recepient
		$mail->addTo($this->get('toEmail'), $this->get('toName'));

		if ($this->_config->send)
			// send it out
			$mail->send();
	}

        /**
         * Creates an instance of class Zend_Mail
         *
         * @return Zend_Mail
         */
	protected function _createZendMailer () {
		return new Zend_Mail('windows-1251');
	}

}
