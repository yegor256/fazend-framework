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
 * One abstract field
 *
 * @package Model_Form
 */
abstract class FaZend_View_Helper_Forma_Field
{

    /**
     * List of directories where to find plugins
     *
     * @var string[]
     */
    protected static $_pluginDirs = array();

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
     * Converters
     *
     * The array
     *
     * @var array
     * @see deriveValue()
     * @see _setConvertTo()
     */
    protected $_converters = array();
    
    /**
     * Add new directory to list of dirs where to find plugins
     *
     * @param string Class name prefix
     * @param string Absolute path to the dir
     * @return void
     */
    public static function addPluginDir($prefix, $dir) 
    {
        self::$_pluginDirs[$prefix] = $dir;
    }

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
     * @return FaZend_View_Helper_Form_Field
     * @throws FaZend_View_Helper_Forma_Field_NotFound
     */
    public static function factory($type, FaZend_View_Helper_Forma $helper)
    {
        foreach (self::$_pluginDirs as $prefix=>$dir) {
            $file = $dir . '/Field' . ucfirst($type) . '.php';
            if (file_exists($file)) {
                require_once $file;
                $className = $prefix . ucfirst($type);
                return new $className($helper);
            }
        }
        FaZend_Exception::raise(
            'FaZend_View_Helper_Forma_Field_NotFound',
            "Plugin '{$type}' not found"
        );
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
     * @throws FaZend_View_Helper_Forma_InvalidOption
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'field') !== 0)
            return call_user_func_array(array($this->_helper, $method), $args);

        // ->fieldRequired(...) will be converted to _setRequired(...)
        $func = '_set' . substr($method, 5);
        if (!method_exists($this, $func)) {
            FaZend_Exception::raise(
                'FaZend_View_Helper_Forma_InvalidOption', 
                "Method '{$func}' is unknown in " . get_class($this)
            );
        }
            
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
     * @throws FaZend_View_Helper_Forma_InvalidConverter
     * @uses $_converters
     **/
    public function deriveValue(Zend_Form_Element $element)
    {
        $value = $element->getValue();
        
        foreach ($this->_converters as $converter) {
            // maybe scalar type is expected?    
            switch (strtolower($converter['type'])) {
                case 'integer':
                    $value = intval($value);
                    continue;
                case 'bool':
                case 'boolean':
                    $value = (bool)$value;
                    continue;
                case 'float':
                    $value = (float)$value;
                    continue;
                case 'string':
                    $value = strval($value);
                    continue;
                default:
                    $class = $converter['type'];
                    if (!class_exists($class))
                        FaZend_Exception::raise(
                            'FaZend_View_Helper_Forma_InvalidConverter', 
                            "Class '{$class}' is unknown"
                        );

                    if (empty($converter['method'])) {
                        $value = new $class($value);
                        continue;
                    }

                    if (!method_exists($class, $converter['method']))
                        FaZend_Exception::raise(
                            'FaZend_View_Helper_Forma_InvalidConverter', 
                            "Method '{$converter['method']}' is absent in $class"
                        );

                    $value = call_user_func_array(
                        array(
                            $class,
                            $converter['method']
                        ), array($value)
                    );
                    break;
            }
        }
        return $value;
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
            ->addDecorator(
                'HtmlTag', 
                array(
                    'tag'=>'dd'
                )
            );

        if (isset($this->_label)) {
            $element
                ->setLabel(
                    $this->_label .
                    ($this->_required ? "<span style='color:red;'>*</span>" : false) . ':'
                )
                ->addDecorator(
                    'Label', 
                    array(
                        //'tag'=>'dt',
                        'escape'=>false
                    )
                );
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
     * @return $this
     */
    protected function _setLabel($label)
    {
        $this->_label = $label;
        return $this;
    }

    /**
     * Setter, to add help message to the field
     *
     * @param string Help to show below the field
     * @return $this
     */
    protected function _setHelp($help)
    {
        $this->_help = $help;
        return $this;
    }

    /**
     * Setter, to add value to the field
     *
     * @param string Value to show in the field
     * @return $this
     */
    protected function _setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * This field is required
     *
     * @param boolean Is it required?
     * @return $this
     */
    protected function _setRequired($required = true)
    {
        $this->_required = $required;
        return $this;
    }

    /**
     * Set new HTML attribute
     *
     * @param string Attribute name
     * @param string Attribute value
     * @return $this
     */
    protected function _setAttrib($attrib, $value)
    {
        $this->_attribs[$attrib] = $value;
        return $this;
    }

    /**
     * Set new validator
     *
     * @param callback Validator of the field value
     * @return $this
     */
    protected function _setValidator($validator)
    {
        $this->_validators[] = $validator;
        return $this;
    }

    /**
     * Set type to be used in conversion
     *
     * @param string Type name
     * @param string Method name to use for conversion
     * @return $this
     * @uses $_converters
     */
    protected function _setConverter($type, $method = null)
    {
        $this->_converters[] = array(
            'type' => $type,
            'method' => $method
        );
        return $this;
    }

}
