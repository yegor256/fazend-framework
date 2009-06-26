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
* CLI Os version detector
*
*
*/
class OSVersion extends FaZend_Cli_Abstract {

    /**
     * Executor of a command-line command
     *
     * @return string
     */
    public function execute() {

        $sub = $this->_callCli('Sub');
        
        return $sub . shell_exec('ver');

    }
    

}
