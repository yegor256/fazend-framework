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
        if (strpos($method, 'field') !== 0) {
            return call_user_func_array(array($this->_helper, $method), $args);
        }

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
        if (method_exists($this, $method)) {
            return $this->$method();
        }
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
            $value = $converter->call($value);
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
                ->setLabel($this->_label)
                ->addDecorator(
                    'Label', 
                    array(
                        //'tag'=>'dt',
                        'escape'=>false
                    )
                );
        }

        if (isset($this->_value)) {
            $element->setValue($this->_value);
        }

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
     * @param mixed Index of the suffix, from FaZend_View_Helper_Forma::$_labelSuffixes
     * @return $this
     * @see FaZend_View_Helper_Forma::setLabelSuffixes
     */
    protected function _setLabel($label, $suffix = false)
    {
        $this->_label = $label;
        $suffixes = FaZend_View_Helper_Forma::getLabelSuffixes();
        if (isset($suffixes[$suffix])) {
            $this->_label . $suffixes[$suffix];
        }
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
     * Set type to be used in conversion
     *
     * @param callback
     * @return $this
     * @uses $_converters
     */
    protected function _setConverter($callback)
    {
        $this->_converters[] = FaZend_Callback::factory($callback);
        return $this;
    }

}
