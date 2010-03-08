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
     * List of label suffixes
     *
     * @var string
     * @see setLabelSuffixes()
     */
    protected static $_labelSuffixes = array();

    /**
     * Unique ID of the helper on the page
     *
     * @var mixed
     */
    protected $_id;

    /**
     * Form to render
     *
     * @var Zend_Form
     */
    protected $_form;
    
    /**
     * Form was completed?
     *
     * @var boolean
     * @see _render()
     */
    protected $_completed = false;

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
     * Set list of suffixes to be used in fieldLabel()
     *
     * You should call this method in your bootstrap and provide an
     * array of suffixes you might use for your labels. Normally you
     * configure TRUE/FALSE suffixes for required/non-required fields,
     * for example:
     *
     * <code>
     * FaZend_View_Helper_Forma::setLabelSuffixes(
     *   array(
     *     true => '<span style="color:red">*:</span>',
     *     false => ':',
     *   )
     * );
     * </code>
     * 
     * Then, in your view script you add a label to the field, providing
     * the index of the suffix (FALSE is used by default), e.g.:
     *
     * <code>
     * <?=$this->forma()
     *   ->addField('text', 'email')
     *     ->fieldLabel('Your email', true)
     *   ?>
     * </code>
     *
     * @param array List of suffixes
     * @return void
     */
    public static function setLabelSuffixes(array $labelSuffixes) 
    {
        self::$_labelSuffixes = $labelSuffixes;
    }
    
    /**
     * Get label suffixes
     *
     * @return array
     */
    public static function getLabelSuffixes() 
    {
        return self::$_labelSuffixes;
    }

    /**
     * Builds the object
     *
     * @param mixed|null Name of the form instance
     * @return FaZend_View_Helper_Forma
     */
    public function forma($id = null) 
    {
        if (is_null($id)) {
            $id = 1;
            while (isset(self::$_instances[$id])) {
                $id += 1;
            }
        }
        if (!isset(self::$_instances[$id])) {
            self::$_instances[$id] = new self();
            self::$_instances[$id]->_form = new FaZend_Form();
            self::$_instances[$id]->_id = $id;
        }
        return self::$_instances[$id];
    }
    
    /**
     * Form was completed?
     *
     * @return boolean
     */
    public function isCompleted() 
    {
        return $this->_completed;
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    public function __toString() 
    {
        try {
            $html = (string)$this->_render();
        } catch (Exception $e) {
            $html = get_class($this) . ' throws ' . get_class($e) . ': ' . $e->getMessage();
        }
        return $html;
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
     * @return $this
     */
    public function setFormAction($url) 
    {
        $this->_form->setAction($url);
        return $this;
    }

    /**
     * Get param from POST
     *
     * Retrieve param using POST data and form configuration
     *
     * @param string What parameter we are looking for...
     * @return mixed|null
     * @throws FaZend_View_Helper_Forma_ParamNotFound
     */
    public function getParam($name) 
    {
        // encode it first
        $formName = $this->_makeFieldName($name);
        
        // maybe this element is absent in the form?
        if (!isset($this->_form->$formName)) {
            FaZend_Exception::raise(
                'FaZend_View_Helper_Forma_ParamNotFound',
                "Field '{$name}' not found in forma, but is required by the action"
            );
        }

        // get the value of this element from the form
        return $this->_fields[$name]->deriveValue($this->_form->$formName);
    }

    /**
     * Converts it to HTML
     *
     * @return string HTML
     */
    protected function _render() 
    {
        // configure the form
        $this->_form->setView($this->getView())
            ->setMethod('post')
            ->setDecorators(array())
            ->addDecorator('FormElements')
            ->addDecorator('Form');

        // add all input elements to the form
        foreach ($this->_fields as $name=>$field) {
            $element = $field->getFormElement($this->_makeFieldName($name));
            $this->_form->addElement($element);
        }

        // show the form again, if it's not filled and completed
        $log = '';
        $args = array();
        $this->_completed = ($this->_form->isFilled() && $this->_process($log, $args));
        $html = strval($this->_form->__toString());
        
        // if the form was NOT completed yet - just show it
        if (!$this->_completed) {
            return $html;
        }

        // if no behaviors were specified, we use the default one
        if (!count($this->_behaviors)) {
            $this->addBehavior('showLog');
        }
        
        // run them all one by one
        foreach ($this->_behaviors as $behavior) {
            $behavior->setMethodArgs($args);
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
        foreach (array_keys($this->_fields) as $id) {
            $matches = array();
            if (preg_match('/^field(\d+)$/', $id, $matches)) {
                $newId = (int)$matches[1] + 1;
            }
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
    protected function _process(&$log, array &$args) 
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

        // if ACTION is specified in the submit button
        $field = $this->_fields[$this->_revertFieldName($submit->getName())];
        if ($field->action) {
            // get callback params from the clicked button
            $inputs = $field->action->getInputs();

            try {
                // run through all required paramters. required by method
                foreach ($inputs as $input) {
                    // get value of this parameter from form
                    $args[$input] = $this->getParam($input);
                }

                // make a call
                call_user_func_array(
                    array(
                        $field->action, 
                        'call'
                    ),
                    $args
                );

                // it's done, if we're here and no exception has been thrown
                $result = true;
            } catch (Exception $e) {
                // add error message to the submit button we pressed
                $submit->addError($e->getMessage());
                // and the result is false
                $result = false;
            }
        } else {
            $result = true;
        }

        // save log into INPUT variable, by reference (see function definition above)
        $log = FaZend_Log::getInstance()->getWriter(spl_object_hash($this))->getLog();
        FaZend_Log::getInstance()->removeWriter(spl_object_hash($this));

        // return boolean result
        return $result;
    }
    
    /**
     * Make unique field name
     *
     * @param string Name of the field
     * @return string
     */
    protected function _makeFieldName($name) 
    {
        return $this->_id . '__' . $name;
    }

    /**
     * Revert field name from a unique name
     *
     * @param string Name of the field
     * @return string
     */
    protected function _revertFieldName($id) 
    {
        return substr(strchr($id, '__'), 2);
    }

}
