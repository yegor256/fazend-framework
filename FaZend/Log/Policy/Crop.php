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
 * Crop the file, keep its size lower than maximum
 *
 * @package Log
 * @see logg()
 */
class FaZend_Log_Policy_Crop extends FaZend_Log_Policy_Abstract
{

    /**
     * List of available options
     *
     * @var array
     */
    protected $_options = array(
        'length' => 50, // maximum length of file in Kb
    );

    /**
     * Run the policy
     *
     * @return void
     * @throws FaZend_Log_Policy_Email_Exception
     */
    protected function _run()
    {
        /**
         * Here we find all reasons why this file should be sent
         * by email to admin
         */
        if (@filesize($this->_file) < $this->_options['length'] * 1024) {
            return;
        }

        /**
         * @todo implement it properly! we should NOT destroy the file here,
         *       but remove the first X lines of it
         */
        $this->_truncate($this->_file);
    }

}
