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
 * Form to show
 *
 * <code>
 * <?php echo $this->forma()
 *    ->addBehavior('forward', 'index', 'index')
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
     * Instances of the helper
     *
     * @var FaZend_View_Helper_Forma[]
     */
    protected static $_instances = array();

    /**
     * Form to render
     *
     * @var Zend_Form
     */
    protected $_form;

    /**
     * Fields
     *
     * @var FaZend_View_Helper_Forma_Field[]
     */
    protected $_fields = array();
    
    /**
     * What to do when the form is completed?
     *
     * @var FaZend_View_Helper_Forma_Behavior_Abstract
     **/
    protected $_behaviors = array();

    /**
     * Builds the object
     *
     * @param mixed Name of the form instance
     * @return FaZend_View_Helper_Forma
     */
    public function forma($id = 1) 
    {
        self::$_instances[$id] = new FaZend_View_Helper_Forma();
        self::$_instances[$id]->_form = new FaZend_Form();
        return self::$_instances[$id];
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
     * Add attribute to the forma
     *
     * @param string Attribute name
     * @param string Attribute value
     * @return $this
     */
    public function addAttrib($attrib, $value) 
    {
        $this->_form->setAttrib($attrib, $value);
        return $this;
    }

    /**
     * Add new behavior to the form
     *
     * @param string Name of the behavior
     * @return Helper_Forma
     */
    public function addBehavior($type /*, ... */) 
    {
        $className = 'FaZend_View_Helper_Forma_Behavior_' . ucfirst($type);
        $args = func_get_args();
        array_shift($args);
        $this->_behaviors[] = new $className($args);
        return $this;
    }
    
    /**
     * Set form ACTION URL
     *
     * @param string URL to set
     * @return void
     */
    public function setFormAction($url) 
    {
        $this->_form->setAction($url);
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function _render() 
    {
        // configure the form
        $this->_form->setView($this->getView())
            ->setMethod('post')
            ->setDecorators(array())
            ->addDecorator('FormElements')
            ->addDecorator('Form');

        // add all input elements to the form
        foreach ($this->_fields as $name=>$field)
            $this->_form->addElement($field->getFormElement($name));

        // show the form again, if it's not filled and completed
        $log = '';
        $methodArgs = array();
        $completed = ($this->_form->isFilled() && $this->_process($log, $methodArgs));
        $html = strval($this->_form->__toString());
        
        // if the form was NOT completed yet - just show it
        if (!$completed)
            return "<p>{$html}</p>";

        // if no behaviors were specified, we use the default one
        if (!count($this->_behaviors))
            $this->addBehavior('showLog');
        
        // run them all one by one
        foreach ($this->_behaviors as $behavior) {
            $behavior->setMethodArgs($methodArgs);
            $behavior->run($html, $log);
        }
            
        // return the resulted HTML, after all behavior(s)
        return $html;
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
                FaZend_Exception::raise(
                    'FaZend_View_Helper_Forma_FieldAlreadyExists', 
                    "Field '{$name}' already exists in the form"
                );
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
     * @param string Log to save
     * @param array List of params to be passed to method
     * @return boolean Processed without errors?
     */
    protected function _process(&$log, array &$methodArgs) 
    {
        // start logging everything into a new logger
        FaZend_Log::getInstance()->addWriter('Memory', spl_object_hash($this));

        // HTTP POST request holder
        $request = Zend_Controller_Front::getInstance()->getRequest();

        // find the clicked button
        foreach ($this->_form->getElements() as $element) {
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
        $mnemos = array();

        try {
            // run through all required paramters. required by method
            foreach ($rMethod->getParameters() as $param) {
                // get value of this parameter from form
                $methodArgs[$param->name] = $this->_getFormParam($param);
                // this is necessary for logging (see below)
                switch (true) {
                    case is_bool($methodArgs[$param->name]):
                        $mnemo = $methodArgs[$param->name] ? 'TRUE' : 'FALSE';
                        break;
                    case is_scalar($methodArgs[$param->name]):
                        $mnemo = "'" . cutLongLine($methodArgs[$param->name]) . "'";
                        break;
                    case is_object($methodArgs[$param->name]):
                        $mnemo = get_class($methodArgs[$param->name]);
                        break;
                    case is_null($methodArgs[$param->name]):
                        $mnemo = 'NULL';
                        break;
                    default:
                        $mnemo = '???';
                        break;
                }
                $mnemos[] = $mnemo;
            }

            // log this operation
            logg(
                "Calling %s::%s(%s)",
                $rMethod->getDeclaringClass()->name,
                $method,
                implode(', ', $mnemos)
            );

            // execute the target method
            $return = call_user_func_array(array($class, $method), $methodArgs);
            
            // it's done, if we're here and no exception has been thrown
            $result = true;
        } catch (Exception $e) {
            // add error message to the submit button we pressed
            $submit->addError($e->getMessage());
            // and the result is false
            $result = false;
        }

        // save log into INPUT variable, by reference (see function definition above)
        $log = FaZend_Log::getInstance()->getWriter(spl_object_hash($this))->getLog();
        FaZend_Log::getInstance()->removeWriter(spl_object_hash($this));

        // return boolean result
        return $result;
    }

    /**
     * Get param from POST
     *
     * Retrieve param using POST data and form configuration
     *
     * @param ReflectionParameter What parameter we are looking for...
     * @return mixed|null
     * @throws FaZend_View_Helper_Forma_ParamNotFound
     */
    protected function _getFormParam(ReflectionParameter $param) 
    {
        // this is a name of element in the form, which we expect to send to the method
        $name = $param->name;
        
        // maybe this element is absent in the form?
        if (!isset($this->_form->$name)) {
            if ($param->isOptional()) {
                return $param->getDefaultValue();
            } else {
                FaZend_Exception::raise(
                    'FaZend_View_Helper_Forma_ParamNotFound',
                    "Field '{$name}' not found in forma, but is required by the action"
                );
            }
        }

        // get the value of this element from the form
        return $this->_fields[$name]->deriveValue($this->_form->$name);
    }

}
