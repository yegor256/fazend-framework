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
 * @see FaZend_Controller_Action
 */
require_once 'FaZend/Controller/Action.php';

/**
 * default ErrorController
 *
 * @package controllers
 */
class Fazend_ErrorController extends FaZend_Controller_Action
{

    /**
     * Email of the admin
     *
     * @var string
     * @see setAdminEmail()
     */
    protected static $_adminEmail = null;

    /**
     * Set email to use as admin's address
     *
     * @param string Email to use for errors delivery
     * @return void
     * @see FaZend_Application_Resource_fz_errors::init()
     */
    public static function setAdminEmail($email)
    {
        self::$_adminEmail = $email;
    }

    /**
     * Not found action
     *
     * @return void
     * @see errorAction()
     */
    public function notfoundAction()
    {
        // 404 error -- controller or action not found
        $this->getResponse()->setHttpResponseCode(404);
        $this->_forward('index', 'index', 'default');
    }

    /**
     * Error action
     *
     * errorAction() is the action that will be called by the "ErrorHandler"
     * plugin.  When an error/exception has been encountered
     * in a ZF MVC application (assuming the ErrorHandler has not been disabled
     * in your bootstrap) - the Errorhandler will set the next dispatchable
     * action to come here.  This is the "default" module, "error" controller,
     * specifically, the "error" action.  These options are configurable.
     *
     * @see http://framework.zend.com/manual/en/zend.controller.plugins.html
     * @return void
     */
    public function errorAction()
    {
        // Ensure the default view suffix is used so we always return good
        // content
        $this->_helper->viewRenderer->setViewSuffix('phtml');

        // Grab the error object from the request
        $errors = $this->_getParam('error_handler');

        // if this is a broken URL
        $exceptions = $this->getResponse()->getException();
        $exception = $exceptions[0];

        // if (get_class($exception) == 'FaZend_Controller_Action_ParamNotFoundException') {
        //     return $this->_redirectFlash($exception->getMessage());
        // }

        // $errors will be an object set as a parameter of the request object,
        // type is a property
        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $this->_redirectFlash(
                    _t('Error 404: page not found'),
                    'notfound'
                );

            default:
                // generate error code
                $errorCode = rand(100, 999);
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = _t(
                    'Internal application error #%d',
                    $errorCode
                );
        }

        // pass the actual exception object to the view
        $this->view->exception = $errors->exception;

        // pass the request to the view
        $this->view->request = $errors->request;

        // shall we show this error to the user?
        $this->view->showError = ini_get('display_errors');

        // notify admin by email
        if (self::$_adminEmail) {
            $lines = array();
            foreach (debug_backtrace() as $line) {
                $lines[] = isset($line['file']) ? "{$line['file']} ({$line['line']})" : false;
            }

            $siteName = parse_url(WEBSITE_URL, PHP_URL_HOST);
            // send email to the site admin admin
            FaZend_Email::create('fazendException.tmpl')
                ->set('toEmail', self::$_adminEmail)
                ->set('toName', 'admin')
                ->set(
                    'subject',
                    $siteName . ' internal PHP error, rev.' .
                    FaZend_Revision::get() .
                    (isset($_SERVER['REQUEST_URI']) ? ', ' . $_SERVER['REQUEST_URI'] : false)
                )
                ->set(
                    'text',
                    get_class($errors->exception) . ': ' . $errors->exception->getMessage() . "\n\n" .
                    implode("\n", $lines) . "\n\n" .
                    print_r($errors->request->getParams(), true) . "\n\n" .
                    $errors->exception->getTraceAsString()
                )
                ->set('errorCode', $errorCode)
                ->send();

            FaZend_Log::err(get_class($errors->exception) . ': ' . $errors->exception->getMessage());
        }
    }
}
