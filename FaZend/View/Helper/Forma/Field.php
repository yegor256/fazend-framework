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
 * @author Yegor Bugaenko <egor@technoparkcorp.com>
 * @copyright Copyright (c) TechnoPark Corp., 2001-2009
 * @version $Id$
 *
 */

/**
 * One abstract field
 *
 * @package Model_Form
 */
abstract class FaZend_View_Helper_Forma_Field {

    /**
     * Helper instance
     *
     * @var FaZend_View_Helper_Forma
     */
    protected $_helper;

    /**
     * HTML attributes
     *
     * @var array
     */
    protected $_attribs = array();

    /**
     * Is it required?
     *
     * @var boolean
     */
    protected $_required = true;

    /**
     * Label above the element
     *
     * @var string
     */
    protected $_label;

    /**
     * Field value
     *
     * @var string
     */
    protected $_value;

    /**
     * Help below the element
     *
     * @var string
     */
    protected $_help;

    /**
     * Value validators
     *
     * @var array
     */
    protected $_validators = array();

    /**
     * Convert to
     *
     * @var array|null
     */
    protected $_convertTo = null;

    /**
     * Private constructor
     *
     * @return void
     */
    protected function __construct(FaZend_View_Helper_Forma $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Factory method
     *
     * @param string Type of field
     * @param Helper_Forma Form, the owner
     * @return Model_Form_Field
     * #throws Model_Form_Field_ClassNotFound
     */
    public static function factory($type, FaZend_View_Helper_Forma $helper)
    {
        require_once 'FaZend/View/Helper/Forma/Field' . ucfirst($type) . '.php';
        $className = 'FaZend_View_Helper_Forma_Field' . ucfirst($type);
        return new $className($helper);
    }

    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    public function getFormElement($name)
    {
        $element = $this->_getFormElement($name);
        $this->_configureFormElement($element);
        return $element;
    }

    /**
     * Form method gateway
     *
     * @return string
     */
    public function __toString()
    {
        return $this->_helper->__toString();
    }

    /**
     * Call catcher
     *
     * @param string Method name
     * @param array List of params
     * @return value
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'field') !== 0)
            return call_user_func_array(array($this->_helper, $method), $args);

        // ->fieldRequired(...) will be converted to _setRequired(...)
        $func = '_set' . substr($method, 5);
        if (!method_exists($this, $func))
            FaZend_Exception::raise(
                'FaZend_View_Helper_Forma_InvalidOption', 
                "Method '{$func}' is unknown in " . get_class($this)
            );
            
        call_user_func_array(array($this, $func), $args);

        return $this;
    }

    /**
     * Get variable from inside
     *
     * @param string Method name
     * @return value
     */
    public function __get($name)
    {
        $method = '_get' . ucfirst($name);
        if (method_exists($this, $method))
            return $this->$method();
        return $this->{'_' . $name};
    }

    /**
     * Get value from the form element
     *
     * @param Zend_Form_Element The element to work with
     * @return mixed
     **/
    public function deriveValue(Zend_Form_Element $element)
    {
        $value = $element->getValue();
        if (is_null($this->_convertTo))
            return $value;
        
        // maybe scalar type is expected?    
        switch (strtolower($this->_convertTo['type'])) {
            case 'integer':
                return intval($value);
            case 'bool':
            case 'boolean':
                return (bool)$value;
            case 'float':
                return (float)$value;
            case 'string':
                return strval($value);
            default:
                // do nothing, go ahead
        }
        
        $class = $this->_convertTo['type'];
        if (empty($this->_convertTo['method']))
            return new $class($value);
            
        return call_user_func_array(
            array(
                $class,
                $this->_convertTo['method']
            ), array($value)
        );
    }

    /**
     * Configure form element
     *
     * @param Zend_Form_Element The element to configure
     * @return void
     */
    protected function _configureFormElement(Zend_Form_Element $element)
    {
        $element->setDecorators(array())
            ->addDecorator('ViewHelper')
            ->addDecorator('Errors')
            ->addDecorator('HtmlTag', array(
                'tag'=>'dd'));

        if (isset($this->_label)) {
            $element->setLabel($this->_label .
                ($this->_required ? "<span style='color:red;'>*</span>" : false) . ':')
                ->addDecorator('Label', array(
                    //'tag'=>'dt',
                    'escape'=>false));
        }

        if (isset($this->_value))
            $element->setValue($this->_value);

        if ($this->_required)
            $element->setRequired(true);

        foreach ($this->_attribs as $name=>$value) {
            $element->setAttrib($name, $value);
        }
    }

    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    abstract protected function _getFormElement($name);

    /**
     * Setter, to add label to the field
     *
     * @param string Label to show above the field
     * @return void
     */
    protected function _setLabel($label) {
        $this->_label = $label;
    }

    /**
     * Setter, to add help message to the field
     *
     * @param string Help to show below the field
     * @return void
     */
    protected function _setHelp($help) {
        $this->_help = $help;
    }

    /**
     * Setter, to add value to the field
     *
     * @param string Value to show in the field
     * @return void
     */
    protected function _setValue($value) {
        $this->_value = $value;
    }

    /**
     * This field is required
     *
     * @param boolean Is it required?
     * @return void
     */
    protected function _setRequired($required = true)
    {
        $this->_required = $required;
    }

    /**
     * Set new HTML attribute
     *
     * @param string Attribute name
     * @param string Attribute value
     * @return void
     */
    protected function _setAttrib($attrib, $value)
    {
        $this->_attribs[$attrib] = $value;
    }

    /**
     * Set new validator
     *
     * @param callback Validator of the field value
     * @return void
     */
    protected function _setValidator($validator)
    {
        $this->_validators[] = $validator;
    }

    /**
     * Set type to be used in conversion
     *
     * @param string Type name
     * @param string Method name to use for conversion
     * @return void
     */
    protected function _setConvertTo($type, $method = null)
    {
        $this->_convertTo = array(
            'type' => $type,
            'method' => $method
        );
    }

}
