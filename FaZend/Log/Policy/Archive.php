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
 * @version $Id: Log.php 1747 2010-03-17 19:17:38Z yegor256@gmail.com $
 * @category FaZend
 */

/**
 * Archive the file every day/week/month...
 *
 * @package Log
 * @see logg()
 */
class FaZend_Log_Policy_Archive extends FaZend_Log_Policy_Abstract
{

    /**
     * List of available options
     *
     * @var array
     */
    protected $_options = array(
        'mask' => '-mdY.gz', // mask for every archive, the same format as for date()
        'period' => '2days', // when to create a new archive
        'history' => '1week', // how long to keep the files in archive
    );

    /**
     * Run the policy
     *
     * @return void
     * @throws FaZend_Log_Policy_Email_Exception
     */
    protected function _run()
    {
        // not implemented yet...
    }

}
