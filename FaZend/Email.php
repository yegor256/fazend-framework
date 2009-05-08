<?php
/**
 *
 * Copyright (c) 2009, FaZend.com
 * All rights reserved. THIS IS PRIVATE SOFTWARE.
 *
 * Redistribution and use in source and binary forms, with or without modification, are PROHIBITED
 * without prior written permission from the author. This product may NOT be used anywhere
 * and on any computer except the server platform of FaZend.com. located at
 * www.FaZend.com. If you received this code occacionally and without intent to use
 * it, please report this incident to the author by email: privacy@FaZend.com
 *
 * @copyright Copyright (c) FaZend.com, 2009
 * @version $Id$
 *
 */

/**
 * Send email using template
 *
 * @package FaZend 
 */
class FaZend_Email {

	private $variables;

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

		$this->set('fromEmail', Zend_Registry::getInstance()->configuration->email->notifier->email);
		$this->set('fromName', Zend_Registry::getInstance()->configuration->email->notifier->name);

		$this->set('toEmail', Zend_Registry::getInstance()->configuration->email->manager->email);
		$this->set('toName', Zend_Registry::getInstance()->configuration->email->manager->name);
	}

        /**
         * Set local value
         *
         * @return void
         */
	public function set ($key, $value) {
		$this->variables[$key] = $value;
		return $this;
	}

        /**
         * Get local value
         *
         * @return var
         */
	public function get ($key) {
		return $this->variables[$key];
	}

        /**
         * Sends the email
         *
         * @return void
         */
	public function send () {
	        // this class will send email
		$mail = $this->createZendMailer();

		// we render the template by means of View
		$view = new Zend_View();

		// in this folder all email templates are located
		$view->setScriptPath(APPLICATION_PATH . Zend_Registry::getInstance()->configuration->email->folder);

		// set all variables to View for rendering
		foreach ($this->variables as $key=>$value)
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

		if (Zend_Registry::getInstance()->configuration->email->send)
			// send it out
			$mail->send();
	}

        /**
         * Creates an instance of class Zend_Mail
         *
         * @return Zend_Mail
         */
	private function createZendMailer () {
		return new Zend_Mail('windows-1251');
	}

}
