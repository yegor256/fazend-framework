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
 * Mockup meta element
 *
 * @package FaZend 
 */
interface FaZend_UiModeller_Mockup_Meta_Interface {

    /**
     * Draw 
     *
     * @return int Height of the element
     */
    public function draw($y);

    /**
     * Set label
     *
     * @return void
     */
    public function setLabel($label);

}
