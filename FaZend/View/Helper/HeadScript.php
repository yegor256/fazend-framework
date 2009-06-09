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
	 * @return string
	 */
	public function toString($indent = null) {

		$container = $this->getContainer();

		$new = new Zend_View_Helper_Placeholder_Container();

		$aggregator = null;
		foreach($container as $id=>$script) {
			// so we meet new text script
			if (($script->type == 'text/javascript') && !isset($script->attributes['src']) && !empty($script->source)) {
				
				if (!$aggregator)
					$aggregator = $script;

				$aggregator->source .= $script->source;	

				continue;

			// we had some texts before	
			}
			
			if ($aggregator) {
				
				$new[] = $aggregator;
				$aggregator = null;

			}

			$new[] = $script;
		}	

		// if we still have something in the aggregator
		if ($aggregator) {
			$new[] = $aggregator;

		$this->setContainer($new);

		return parent::toString($indent);

	}

}
