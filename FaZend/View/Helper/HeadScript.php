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

require_once 'FaZend/View/Helper/HeadScript.php';

/**
 * Instead of a standard headScript()
 *
 * It is packing together inline scripts into one single <SCRIPT> block
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_HeadScript extends Zend_View_Helper_HeadScript
{

    /**
     * Compress all scripts into one text
     *
     * Inline scripts written one after one should be compressed into
     * one script. For the sake of space saving.
     *
     * This method is executed when it's time to show all script blocks,
     * hopefully at the END of your HTML, right before <BODY> tag.
     *
     * @param boolean We don't touch this parameter, just pass it to the parent
     * @return string
     */
    public function toString($indent = null)
    {
        // get the existing container, from the parent class
        $container = $this->getContainer();

        // create new container, where new <SCRIPT> blocks will be placed
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

        // save new container to the parent class
        $this->setContainer($new);

        // parent class will output the new structure of <SCRIPT> blocks
        return parent::toString($indent);
    }

}
