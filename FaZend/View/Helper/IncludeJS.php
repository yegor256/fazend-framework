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
 * @see FaZend_View_Helper
 */
require_once 'FaZend/View/Helper.php';

/**
 * Include single JS file
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_IncludeJS extends FaZend_View_Helper
{

    /**
     * Include a JS file as a link
     *
     * @return void
     * @see routes.ini
     */
    public function includeJS($script)
    {
        $this->getView()->headScript()->appendFile(
            $this->getView()->url(
                array(
                    'revision' => FaZend_Revision::get(),
                    'script' => $script
                ), 
                'fz__js', // route name, see routes.ini 
                true,
                true
            )
        );
    }

}
