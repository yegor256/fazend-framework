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
 * @version $Id: Date.php 1601 2010-02-08 12:10:51Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Callback, which uses eval()
 *
 * @package Callback
 */
class FaZend_Callback_Eval extends FaZend_Callback
{

    /**
     * Returns a short name of the callback
     *
     * @return string
     */
    public function getTitle()
    {
        return 'eval';
    }
    
    /**
     * Returns an array of inputs expected by this callback
     *
     * @return string[]
     */
    public function getInputs()
    {
        if (!preg_match_all('/\$\{([ia]?\d+)\}/', $this->_data, $matches)) {
            return 0;
        }
        return array_unique($matches[1]);
    }

    /**
     * Calculate and return
     *
     * @param array Array of params
     * @return mixed
     */
    protected function _call(array $args)
    {
        // in order to inject FALSE into the ZERO position
        array_unshift($args, false);
        
        // replace ${1}, ${2}, etc with arguments provided
        $eval = preg_replace('/\$\{(a\d+)\}/', '$args[${1}]', $this->_data);
        
        // replace ${i1}, ${i2}, etc with injected variables
        $eval = preg_replace('/\$\{(i\d+)\}/', '{$this->_injected["${1}"]}', $eval);
        
        eval('$result = ' . $eval . ';');
        return $result;
    }

}
