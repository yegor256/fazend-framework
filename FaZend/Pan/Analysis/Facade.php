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
 * Facade for Analysis information
 *
 * @package Pan
 * @subpackage Analysis
 */
class FaZend_Pan_Analysis_Facade
{

    /**
     * Get full list of components, as a hierarchy
     *
     * Every element in the array is just a "string" name of the
     * component, not a component itself.
     *
     * @return string[]
     * @see FaZend_Pan_Analysis_Component_System
     */
    public function getComponents()
    {
        $list = array();
        foreach (FaZend_Pan_Analysis_Component_System::getInstance()->getIterator() as $component) {
            $list[] = $component->getFullName(); 
        }
        return $list;
    }

    /**
     * Get full list of components, as an array of arrays
     *
     * Every element of the list is an array, with elements named
     * according to our internal principle.
     *
     * @return array[]
     * @see FaZend_Pan_Analysis_Component_System
     */
    public function getComponentsList()
    {
        $list = array();
        foreach (FaZend_Pan_Analysis_Component_System::getInstance()->getIterator() as $item) {
            $list[] = array(
                'name' => $item->getName(),
                'fullName' => $item->getFullName(),
                'type' => $item->getType(),
                'traces' => $item->getTraces(),
            );
        }
        return $list;
    }
    
}
