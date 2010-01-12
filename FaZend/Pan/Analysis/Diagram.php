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
 * Diagram access point
 *
 * @package AnalysisModeller
 */
class FaZend_Pan_Analysis_Diagram
{

    const SEPARATOR = '-';
    
    const WIDTH = 700;
    const HEIGHT = 400;

    /**
     * Factory method
     *
     * @param string Name of the diagram
     * @return void
     */
    public static function factory($name)
    {
        if (!$name)
            $name = 'System' . self::SEPARATOR . 'partof';
            
        $className = 'FaZend_Pan_Analysis_Diagram_' . ucfirst(substr(strrchr($name, self::SEPARATOR), 1));
        
        Zend_Loader::loadClass($className);
        return new $className($name);
    }
    
    /**
     * Get all available types of diagrams
     *
     * @return string[]
     */
    public static function getTypes()
    {
        $list = array();
        foreach (glob(dirname(__FILE__) . '/Diagram/*.php') as $file) {
            $type = pathinfo($file, PATHINFO_FILENAME);
            if ($type == 'Abstract')
                continue;
            if (!preg_match('/^\w+$/', $type))
                continue;
            $list[] = strtolower($type);
        }
        return $list;
    }
    
}
