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
     * @return string[]|0
     */
    public function getInputs()
    {
        $matches = array();
        if (!preg_match_all('/\$\{(.*?)\}/', $this->_data, $matches)) {
            return 0;
        }
        $inputs = array_unique($matches[1]);
        sort($inputs);
        return array_filter(
            $inputs,
            create_function('$a', 'return !preg_match("/^i\d+$/", $a);')
        );
    }

    /**
     * Calculate and return
     *
     * @param array Array of params
     * @return mixed
     * @throws FaZend_Callback_Eval_MissedArgumentException
     * @throws FaZend_Callback_Eval_MissedInjectionException
     * @throws FaZend_Callback_Eval_SyntaxException
     */
    protected function _call(array $args)
    {
        // replace ${a1}, ${a2}, etc with arguments provided
        $eval = $this->_data;
        $inputs = $this->getInputs();
        if (is_array($inputs)) {
            foreach ($inputs as $input) {
                if (!array_key_exists($input, $args)) {
                    FaZend_Exception::raise(
                        'FaZend_Callback_Eval_MissedArgumentException',
                        "Argument '{$input}' is missed for '{$eval}' amont " .
                        count($args) . " provided: " . implode(', ', array_keys($args))
                    );
                }
                $eval = str_replace("\${{$input}}", "\$args[\"{$input}\"]", $eval);
            }
        }
        
        // replace ${i1}, ${i2}, etc with injected variables
        $eval = preg_replace('/\$\{(i\d+)\}/', '$this->_injected["${1}"]', $eval);
        
        // validation
        if (preg_match_all('/\$this->\_injected\["(i\d+)"\]/', $eval, $matches)) {
            foreach ($matches[1] as $match) {
                if (!array_key_exists($match, $this->_injected)) {
                    FaZend_Exception::raise(
                        'FaZend_Callback_Eval_MissedInjectionException',
                        "Injection '{$match}' is missed for '{$this->_data}'"
                    );
                }
            }
        }

        $result = $previous = md5(microtime(true));
        $eval = '$result = ' . $eval . ';';
        eval($eval);
        if ($result === $previous) {
            FaZend_Exception::raise(
                'FaZend_Callback_Eval_SyntaxException',
                "Syntax error in '{$eval}'"
            );
        }
        return $result;
    }

}
