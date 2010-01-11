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
 * Form to show
 *
 * <code>
 * <?=$this->forma()
 *    ->setBehavior('forward', 'index')
 *    ->addField('text')
 *        ->fieldLabel('My text:')
 *        ->fieldRequired(true)
 *        ->fieldAttrib('maxlength', 45)
 *    ->addField('submit')
 *        ->fieldAction($class, $method)
 *     ?>
 * </code>
 *
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_Forma extends FaZend_View_Helper
{

    /**
     * Fields
     *
     * @var FaZend_View_Helper_Forma_Field[]
     */
    protected $_fields = array();
    
    /**
     * What to do when the form is completed?
     *
     * @var array
     **/
    protected $_behavior = array(
        'type' => 'showLog', // default behavior, just to show LOG
        );

    /**
     * Builds the object
     *
     * @return FaZend_View_Helper_Forma
     */
    public function forma() 
    {
        return $this;
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function __toString() 
    {
        try {
            return (string)$this->_render();
        } catch (Exception $e) {
            return get_class($this) . ' throws ' . get_class($e) . ': ' . $e->getMessage();
        }
    }

    /**
     * Add new field
     *
     * @param string Name of field class
     * @param string|null Name of the field to create
     * @return Helper_Forma
     */
    public function addField($type, $name = null) 
    {
        require_once 'FaZend/View/Helper/Forma/Field.php';
        $field = FaZend_View_Helper_Forma_Field::factory($type, $this);
        $this->_fields[$this->_uniqueName($name)] = $field;
        return $field;
    }

    /**
     * Set behavior
     *
     * @param string Name of the behavior
     * @return Helper_Forma
     */
    public function setBehavior($type /*, ... */) 
    {
        $this->_behavior['type'] = $type;
        $this->_behavior['args'] = func_get_args();
        return $this;
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function _render() 
    {
        $form = new FaZend_Form();

        $form->setView($this->getView())
            ->setMethod('post')
            ->setDecorators(array())
            ->addDecorator('FormElements')
            ->addDecorator('Form');

        foreach ($this->_fields as $name=>$field) {
            $form->addElement($field->getFormElement($name));
        }

        $log = '';
        if (!$form->isFilled() || !$this->_process($form, $log))
            return '<p>' . (string)$form->__toString() . '</p>';
        
        // the form was filled, what to do now?
        switch ($this->_behavior['type']) {
            // show the LOG instead of form, that's it
            case 'showLog':
                return '<pre class="log">' . ($log ? $log : 'done') . '</pre>';
            
            // redirect to another action/controller
            case 'redirect':
                Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')
                    ->gotoSimple($this->_behavior['args'][0], $this->_behavior['args'][0]);
                return;
            
            default:
                return false;
        }
    }

    /**
     * Create unique name
     *
     * @param string Name
     * @return string Name which is unique
     */
    protected function _uniqueName($name) 
    {
        if (!is_null($name)) {
            if (isset($this->_fields[$name])) {
                FaZend_Exception::raise('FaZend_View_Helper_Forma_FieldAlreadyExists', 
                    "Field '{$name}' already exists in the form");
            }
            return $name;
        }

        $newId = 1;
        foreach ($this->_fields as $id=>$field) {
            if (preg_match('/^field(\d+)$/', $id, $matches))
                $newId = (int)$matches[1] + 1;
        }

        return 'field' . $newId;
    }

    /**
     * Process the form and execute what is required
     *
     * @param Zend_Form The form
     * @param string Log to save
     * @return boolean Processed without errors?
     */
    protected function _process(Zend_Form $form, &$log) 
    {
        // start logging everything into a new logger
        FaZend_Log::getInstance()->addWriter('Memory', 'forma');

        // HTTP POST request holder
        $request = Zend_Controller_Front::getInstance()->getRequest();

        // find the clicked button
        foreach ($form->getElements() as $element) {
            if (!$element instanceof Zend_Form_Element_Submit)
                continue;

            // whether this particular form was submitted by this button?
            if ($element->getLabel() == $request->getPost($element->getName())) {
                $submit = $element;
                break;
            }
        }

        // get callback params from the clicked button
        list($class, $method) = $this->_fields[$submit->getName()]->action;

        // prepare method calling params for this button/callback
        $rMethod = new ReflectionMethod($class, $method);
        $methodArgs = $mnemos = array();

        try {

            // run through all required paramters. required by method
            foreach ($rMethod->getParameters() as $param) {
                // get value of this parameter from form
                $methodArgs[$param->name] = $this->_getFormParam($form, $param);
                // this is necessary for logging (see below)
                $mnemos[] = (is_scalar($methodArgs[$param->name]) ? $methodArgs[$param->name] : get_class($methodArgs[$param->name]));
            }

            // log this operation
            logg(
                "Calling %s::%s('%s')",
                $rMethod->getDeclaringClass()->name,
                $method,
                implode("', '", $mnemos)
            );

            // execute the target method
            call_user_func_array(array($class, $method), $methodArgs);
            
            // it's done, if we're here and no exception has been thrown
            $result = true;

        } catch (Exception $e) {

            // add error message to the submit button we pressed
            $submit->addError($e->getMessage());

            // and the result is false
            $result = false;

        }

        // save log into INPUT variable, by reference (see function definition above)
        $log = FaZend_Log::getInstance()->getWriter('forma')->getLog();
        FaZend_Log::getInstance()->removeWriter('forma');

        // return boolean result
        return $result;
    }

    /**
     * Get param from POST
     *
     * Retrieve param using POST data and form configuration
     *
     * @param Zend_Form The form to get params from
     * @param ReflectionParameter What parameter we are looking for...
     * @return class
     * @throws Helper_Forma_ParamNotFound
     */
    protected function _getFormParam(Zend_Form $form, ReflectionParameter $param) 
    {
        // this is a name of element in the form, which we expect to send to the method
        $name = $param->name;
        
        // maybe this element is absent in the form?
        if (!isset($form->$name)) {
            if ($param->isOptional())
                return $param->getDefaultValue();
            else
                FaZend_Exception::raise('FaZend_View_Helper_Forma_ParamNotFound',
                    "Field '{$name}' not found in forma, but is required by action");
        }

        // get the value of this element from the form
        return $this->_fields[$name]->deriveValue($form->$name);
    }

}
