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
 * Full collection of baseline tags
 *
 * @package Pan
 * @subpackage Baseliner
 */
class FaZend_Pan_Baseliner_Map
{
    
    /**
     * List of rules
     *
     * @var array
     */
    protected $_rules = array();
    
    /**
     * Path of files
     *
     * @var string
     */
    protected $_path;

    /**
     * Email of baseliner
     *
     * @var string
     */
    protected $_email;

    /**
     * Construct the class
     *
     * @return void
     */
    public function __construct($path, $email = null)
    {
        $this->_path = $path;
        $this->_email = $email;
    }

    /**
     * Returns email
     *
     * @return string
     */
    public function getEmail() 
    {
        return $this->_email;
    }

    /**
     * Get directory where all maps should be stored
     *
     * @param boolean Create it if it's absent?
     * @return string
     */
    public static function getStorageDir($enforce = false) 
    {
        $path = APPLICATION_PATH . '/../../test/baseline';
        if ($enforce && !file_exists($path))
            mkdir($path);
        if (file_exists($path))
            $path = realpath($path);
        return $path;
    }

    /**
     * Save map in XML format to the path given
     *
     * @param string Absolute name of the file
     * @return void
     */
    public function save($path) 
    {
        $xml = simplexml_load_string('<?xml version="1.0"?><map></map>');
        
        $xml->addChild('baseliner', $this->_email);
        $xml->addChild('date', Zend_Date::now()->getIso());
        $xml->addChild('revision', FaZend_Revision::get());
        
        $rules = $xml->addChild('rules');
        foreach ($this->_rules as $rule) {
            $child = $rules->addChild('rule');
            $child->addChild('type', $rule['type']);
            $child->addChild('constructor', $rule['constructor']);
            $child->addChild('callback', $rule['callback']);
        }
        
        file_put_contents($path, $xml->asXML());
    }
    
    /**
     * Load map from XML format
     *
     * @param string Absolute name of the file
     * @return void
     */
    public function load($path) 
    {
        $xml = simplexml_load_file($path);

        $this->_rules = array();
        foreach ($xml->rules->children() as $rule) {
            $this->_rules[] = array(
                'type' => $rule->type,
                'constructor' => $rule->constructor,
                'callback' => $rule->callback
                );
        }
    }
    
    /**
     * Get full list of rules
     *
     * @return array
     */
    public function getRules() 
    {
        return $this->_rules;
    }
    
    /**
     * Add new rule to the map
     *
     * @param Reflector Information about the added source code element
     * @param string Method of @baseline tag, e.g. "exists"
     * @param array List of params to the method, could be empty
     * @param string Explanation of the baseline, in text
     * @return void
     * @throws FaZend_Pan_Baseliner_Map_InvalidTag
     */
    public function add(Reflector $reflector, $method, array $params, $description) 
    {
        $mthd = '_add' . substr(get_class($reflector), strlen('Zend_Reflection_'));
        $this->$mthd($reflector, $method, $params, $description);
    }
    
    /**
     * Add file
     *
     * @param Reflector Information about the added source code element
     * @param string Method of @baseline tag, e.g. "exists"
     * @param array List of params to the method, could be empty
     * @param string Explanation of the baseline, in text
     * @return void
     * @throws FaZend_Pan_Baseliner_Map_InvalidTag
     */
    protected function _addFile(Reflector $reflector, $method, array $params, $description) 
    {
        switch ($method) {
            case 'exists':
                $callback = '->isExists()';
                break;
            default:
                FaZend_Exception::raise(
                    'FaZend_Pan_Baseliner_Map_InvalidTag',
                    "Method '$method' is not applicable to a file"
                );
        }
        
        $this->_addRule(
            'file', 
            "('" . substr($reflector->getFileName(), strlen($this->_path)+1) . "')",
            $callback
        );
    }

    /**
     * Add class
     *
     * @param Reflector Information about the added source code element
     * @param string Method of @baseline tag, e.g. "exists"
     * @param array List of params to the method, could be empty
     * @param string Explanation of the baseline, in text
     * @return void
     * @throws FaZend_Pan_Baseliner_Map_InvalidTag
     */
    protected function _addClass(Reflector $reflector, $method, array $params, $description) 
    {
        switch ($method) {
            case 'exists':
                $callback = '->isExists()';
                break;
            case 'instanceOf':
                $callback = "->isInstanceOf($params[0])";
                break;
            default:
                FaZend_Exception::raise(
                    'FaZend_Pan_Baseliner_Map_InvalidTag',
                    "Method '$method' is not applicable to a class"
                );
        }
        
        $this->_addRule(
            'class', 
            "('{$reflector->getName()}')",
            $callback
        );
    }

    /**
     * Add function
     *
     * @param Reflector Information about the added source code element
     * @param string Method of @baseline tag, e.g. "exists"
     * @param array List of params to the method, could be empty
     * @param string Explanation of the baseline, in text
     * @return void
     * @throws FaZend_Pan_Baseliner_Map_InvalidTag
     */
    protected function _addFunction(Reflector $reflector, $method, array $params, $description) 
    {
        switch ($method) {
            case 'exists':
                $callback = '->isExists()';
                break;
            default:
                FaZend_Exception::raise(
                    'FaZend_Pan_Baseliner_Map_InvalidTag',
                    "Method '$method' is not applicable to a function"
                );
        }
        
        $this->_addRule(
            'function', 
            "('{$reflector->getFileName()}', '{$reflector->getName()}')",
            $callback
        );
    }

    /**
     * Add method
     *
     * @param Reflector Information about the added source code element
     * @param string Method of @baseline tag, e.g. "exists"
     * @param array List of params to the method, could be empty
     * @param string Explanation of the baseline, in text
     * @return void
     * @throws FaZend_Pan_Baseliner_Map_InvalidTag
     */
    protected function _addMethod(Reflector $reflector, $method, array $params, $description) 
    {
        switch ($method) {
            case 'exists':
                $callback = '->isExists()';
                break;
            default:
                FaZend_Exception::raise(
                    'FaZend_Pan_Baseliner_Map_InvalidTag',
                    "Method '$method' is not applicable to a method"
                );
        }
        
        $this->_addRule(
            'method', 
            "('{$reflector->getDeclaringClass()->getName()}', '{$reflector->getName()}')",
            $callback
        );
    }

    /**
     * undocumented function
     *
     * @param string Type of the rule, subclass in FaZend_Pan_Baseliner_Validator_*
     * @param string Constructor params, like '("file.php")'
     * @param string Callback to use, like '->exists()'
     * @return void
     */
    protected function _addRule($type, $constructor, $callback) 
    {
        $this->_rules[] = array(
            'type' => $type,
            'constructor' => $constructor,
            'callback' => $callback,
        );
    }

}
