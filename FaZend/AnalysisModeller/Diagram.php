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
 * Diagram access point
 *
 * @package AnalysisModeller
 */
class FaZend_AnalysisModeller_Diagram {

    const SEPARATOR = '-';
    
    const WIDTH = 700;
    const HEIGHT = 400;

    /**
     * Factory method
     *
     * @param string Name of the diagram
     * @return void
     */
    public static function factory($name) {
        if (!$name)
            $name = 'System' . self::SEPARATOR . 'partof';
            
        $className = 'FaZend_AnalysisModeller_Diagram_' . ucfirst(substr(strrchr($name, self::SEPARATOR), 1));
        
        Zend_Loader::loadClass($className);
        return new $className($name);
    }
    
    /**
     * Get all available types of diagrams
     *
     * @return string[]
     */
    public static function getTypes() {
        $list = array();
        foreach (new FilesystemIterator(dirname(__FILE__) . '/Diagram') as $file) {
            $type = $file->getBasename('.php');
            if ($type == 'Abstract')
                continue;
            if (!preg_match('/^\w+$/', $type))
                continue;
            $list[] = strtolower($type);
        }
        return $list;
    }
    
}
