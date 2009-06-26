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
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_HeadScript extends Zend_View_Helper_HeadScript {

    /**
     * Compress all scripts into one text
     *
     * Inline scripts written one after one should be compressed into
     * one script. For the sake of space saving.
     *
     * @return string
     */
    public function toString($indent = null) {

        $container = $this->getContainer();

        $new = new Zend_View_Helper_Placeholder_Container();

        foreach($container as $id=>$script) {
            // so we meet new text script
            if (($script->type == 'text/javascript') && empty($script->attributes['src']) && !empty($script->source)) {
                
                if (!isset($aggregator))
                    $aggregator = $script;
                else    
                    $aggregator->source .= $container->getSeparator() . $script->source;    

                continue;

            // we had some texts before    
            }
            
            if (isset($aggregator)) {
                
                $new[] = $aggregator;
                unset($aggregator);

            }

            $new[] = $script;
        }    

        // if we still have something in the aggregator
        if (isset($aggregator)) 
            $new[] = $aggregator;

        $this->setContainer($new);

        return parent::toString($indent);

    }

}
