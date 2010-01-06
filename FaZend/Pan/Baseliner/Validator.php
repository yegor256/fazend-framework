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
        $success = true;
        
        foreach ($map->getRules() as $rule) {
            $className = __CLASS__ . '_' . ucfirst($rule['type']);
            eval("\$validator = new {$className} {$rule['constructor']};");
            
            $validator->setLocation($this->_path);
            
            eval("\$result = \$validator {$rule['callback']};");
            
            if (is_string($result)) {
                $this->_log('failure: ' . $result);
                $success = false;
            } else
                $this->_log("success: {$rule['type']} {$rule['constructor']} {$rule['callback']}");
            
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
