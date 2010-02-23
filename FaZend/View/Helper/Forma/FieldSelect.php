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

require_once 'FaZend/View/Helper/Forma/Field.php';

/**
 * Select field
 *
 * @package Model_Form
 */
class FaZend_View_Helper_Forma_FieldSelect extends FaZend_View_Helper_Forma_Field
{

    /**
     * Callback to use for options parsing
     *
     * @var FaZend_Callback
     * @see _setMask()
     */
    protected $_mask = null;

    /**
     * Callback to use for ID parsing
     *
     * @var FaZend_Callback
     * @see _setIdMask()
     */
    protected $_idMask = null;
    
    /**
     * Shall we use values or IDs?
     *
     * @var boolean
     * @see _setUseValues()
     */
    protected $_useValues = false;

    /**
     * List of options
     *
     * @var array
     * @see _setOptions()
     */
    protected $_options;
    
    /**
     * Get value from the form element
     *
     * @param Zend_Form_Element The element to work with
     * @return mixed
     */
    public function deriveValue(Zend_Form_Element $element)
    {
        $value = parent::deriveValue($element);
        if ($this->_useValues) {
            return $this->_options[$value];
        }
        return $value;
    }

    /**
     * Create and return form element
     *
     * @param string Name of the element
     * @return Zend_Form_Element
     */
    protected function _getFormElement($name)
    {
        return new Zend_Form_Element_Select($name);
    }

    /**
     * Configure form element
     *
     * @param Zend_Form_Element The element to configure
     * @return void
     */
    protected function _configureFormElement(Zend_Form_Element $element)
    {
        parent::_configureFormElement($element);

        $options = $this->_options;
        
        if (!is_null($this->_mask)) {
            foreach ($options as $id=>&$option) {
                $option = $this->_mask->call($option, $id);
            }
        }

        if (!is_null($this->_idMask)) {
            $opts = array();
            foreach ($options as $id=>$option) {
                $opts[$this->_idMask->call($option, $id)] = $option;
            }
            $options = $opts;
        }
        
        // convert every item to string
        // and prepare array of options
        $opts = array();
        foreach ($options as $id=>$option) {
            $opts[$id] = strval($option);
        }

        $element->setMultiOptions($opts);
    }

    /**
     * Set list of options
     *
     * @param array Associative array of options
     * @param boolean Shall we sort the options?
     * @return void
     */
    protected function _setOptions($options, $sort = false)
    {
        $this->_options = $options;
        if ($sort) {
            asort($this->_options);
        }
    }

    /**
     * Set mask callback
     *
     * <code>
     * <?=$this->forma()
     *   ->addField('select', 'user')
     *     ->fieldOptions(Model_User::retrieveAll()) // we get a list of Model_User objects
     *     ->fieldMask('sprintf("(%s): %s", ${a1}->email, ${a1}->name)')
     * </code>
     *
     * In the example above, every element in the list of options will
     * be parsed through the given MASK before using in SELECT. You can
     * use any CALLBACK you wish. Just one parameter will be sent there --
     * the object.
     *
     * @param FaZend_Callback|mixed Callback to use as a mask for every option
     * @return void
     */
    protected function _setMask($callback)
    {
        $this->_mask = FaZend_Callback::factory($callback);
    }

    /**
     * Set mask callback for ID
     *
     * <code>
     * <?=$this->forma()
     *   ->addField('select', 'user')
     *     ->fieldOptions(Model_User::retrieveAll()) // we get a list of Model_User objects
     *     ->fieldMask('sprintf("(%s): %s", ${a1}->email, ${a1}->name)')
     *     ->fieldIdMask('strval(${a1})') // ID of the user
     *     ->fieldConverter('new Model_User(${a1})') // create user by ID selected
     * </code>
     *
     * @param FaZend_Callback|mixed Callback to use as a mask for every ID
     * @return void
     */
    protected function _setIdMask($callback)
    {
        $this->_idMask = FaZend_Callback::factory($callback);
    }
    
    /**
     * Shall we use values (TRUE) or IDs (FALSE)?
     *
     * @return void
     */
    protected function _setUseValues($useValues = true) 
    {
        $this->_useValues = $useValues;
    }

}
