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
 * Validates one map against another and report errors
 *
 * @package Pan
 * @subpackage Baseliner
 */
class FaZend_Pan_Baseliner_Validator
{
    
    /**
     * Location of files
     *
     * @var string
     **/
    protected $_path;

    /**
     * Shall we log all operations to STDOUT?
     *
     * @var boolean
     **/
    protected $_verbose;

    /**
     * Construct the class
     *
     * @param string Location of files
     * @param boolean Notify user by ECHO about the progress?
     * @return void
     */
    public function __construct($path, $verbose = true)
    {
        $this->_path = $path;
        $this->_verbose = $verbose;
    }

    /**
     * Validates
     *
     * @param FaZend_Pan_Baseliner_Map
     * @return boolean OK or not
     */
    public function validate(FaZend_Pan_Baseliner_Map $map)
    {
        $this->_log("Validating: {$map->getEmail()}");
        $success = true;
        
        foreach ($map->getRules() as $rule) {
            $className = __CLASS__ . '_' . ucfirst($rule['type']);
            
            if (!class_exists($className)) {
                $this->_log("\tinternal failure: class $className is missed");
                $success = false;
                continue;
            }
                
            eval("\$validator = new {$className} {$rule['constructor']};");
            
            $validator->setLocation($this->_path);
            
            eval("\$result = \$validator {$rule['callback']};");
            
            if (is_string($result)) {
                $this->_log("\tfailure: {$result}");
                $success = false;
            } else
                $this->_log("\tsuccess: {$rule['type']}{$rule['constructor']}{$rule['callback']}");
            
        }
        return $success;
    }

    /**
     * Log one message
     *
     * @return void
     **/
    protected function _log($msg) 
    {
        if ($this->_verbose)
            echo $msg . "\n";
    }

}
