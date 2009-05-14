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
 * Nice and configurable html table
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_HtmlTable {

	/**
	 * Instance of the view
	 *
	 * @var Zend_View
	 */
	private $_view;

	/**
	 * Paginator to be used
	 *
	 * @var Zend_Paginator
	 */
	private $_paginator;
	
	/**
	 * List of columns defined by set..()
	 *
	 * @var array
	 */
	private $_columns = array();

	/**
	 * List of options defined by set..()
	 *
	 * @var array
	 */
	private $_options = array();

	/**
	* Save view locally
	*
	* @return void
	*/
	public function setView(Zend_View_Interface $view) {
		$this->_view = $view;
	}           

	/**
	* Get view saved locally
	*
	* @return Zend_View
	*/
	public function getView() {
		return $this->_view;
	}

	/**
	* Show the table
	*
	* @return string HTML
	*/
	public function htmlTable() {
		return $this;
	}

	/**
	* Show the table
	*
	* @return string HTML
	*/
	public function __toString() {
		return $this->_render();
	}

	/**
	* Set the paginator
	*
	* @return HtmlTable
	*/
	public function setPaginator($paginator) {
		$this->_paginator = $paginator;
		return $this;
	}

	/**
	* Set the paginator
	*
	* @return HtmlTable
	*/
	public function setParser($column, $func) {
		$this->_column($column)->parser = $func;
		return $this;
	}

	/**
	* Hide one column
	*
	* @return HtmlTable
	*/
	public function hideColumn($column) {
		$this->_column($column)->hidden = true;
		return $this;
	}

	/**
	* Append option to column, instead of 'options' column
	*
	* @return HtmlTable
	*/
	public function appendOptionToColumn($option, $column) {
		$this->_option($option)->toColumn = $column;
		return $this;
	}

	/**
	* Add option
	*
	* @return HtmlTable
	*/
	public function addOption($title, $httpVar, $column, $urlParams) {
		$this->_option($title)->title = $title;
		$this->_option($title)->httpVar = $httpVar;
		$this->_option($title)->column = $column;
		$this->_option($title)->urlParams = $urlParams;
		return $this;
	}

	/**
	* Conditional skip of the option
	*
	* @return HtmlTable
	*/
	public function skipOption($title, $func) {
		$this->_option($title)->skip = $func;
		return $this;
	}

	/**
	* Add column style
	*
	* @return HtmlTable
	*/
	public function addColumnStyle($column, $style) {
		$this->_column($column)->style = $style;
		return $this;
	}

	/**
	* Set column title
	*
	* @return HtmlTable
	*/
	public function setColumnTitle($column, $title) {
		$this->_column($column)->title = $title;
		return $this;
	}

	/**
	* Allow raw HTML output in the column
	*
	* @return HtmlTable
	*/
	public function allowRawHtml($column) {
		$this->_column($column)->rawHtml = true;
		return $this;
	}

	/**
	* Add column link
	*
	* @return HtmlTable
	*/
	public function addColumnLink($title, $httpVar, $column, $urlParams) {
		$link = new FaZend_StdObject();
		$link->title = $title;
		$link->httpVar = $httpVar;
		$link->urlParams = $urlParams;
		$this->_column($column)->link = $link;
		return $this;
	}

	/**
	* Render the table
	*
	* @return HtmlTable
	*/
	protected function _render() {
		if (!count ($this->_paginator))
			return '<p>no data</p>';

		$resultTRs = array();
		$resultTDs = array();
		$options = array();

		foreach ($this->_paginator as $rowOriginal) {

			if (!is_array($rowOriginal))
				$row = $rowOriginal->toArray();
			else
				$row = $rowOriginal;	

			$resultTRs[] = "<tr class='".(fmod (count($resultTRs), 2) ? 'even' : 'odd').
				"' onmouseover='this.className=\"highlight\"' onmouseout='this.className=\"".
				(fmod (count($resultTRs), 2) ? 'even' : 'odd')."\"'>";

			$tds = array();	
			foreach ($row as $title=>$value) {

				// skip this column if required
				if ($this->_column($title)->hidden)
					continue;

				// strip HTML tags	
				if (!$this->_column($title)->rawHtml)
					$value = htmlspecialchars($value);

				// parse the value of this TD	
				if ($this->_column($title)->parser) {
					$parser = $this->_column($title)->parser;
					$value = $parser ($value, $rowOriginal);
				}	

				// attach link to the TD
				if ($this->_column($title)->link) {
					$link = $this->_column($title)->link;
					$value = "<a href='".$this->getView()->url($link->urlParams + 
						array($link->httpVar => $row[$link->column]), 'default', true)."'>{$value}</a>";
				}	

				// append CSS style
				$tds[$title] = "<td".($this->_column($title)->style ? " style='{$this->_column($title)->style}'" : false).">{$value}";
			}	

			if (count($this->_options)) {
				$optString = '<td>';
				foreach ($this->_options as $option) {

					// skip the option
					if ($option->skip) {
						if ($option->skip ($rowOriginal))
							continue;
					}	

					// build the <A HREF> link for this option
					$optLink = "&#32;<a href='".$this->getView()->url(
						$option->urlParams + array($option->httpVar => $row[$option->column]), 'default', true).
						"'>{$option->title}</a>";

					// attach this option to the particular column	
					if ($option->toColumn)	
						$tds[$option->toColumn] .= $optLink;
					else
						$optString .= $optLink;	
				}
				$options[] = $optString;	
			}	

			$resultTDs[] = $tds;
		}

		// build the header using the last ROW information
		$header = '';
		foreach ($row as $title=>$value) {
			// skip the column
			if ($this->_column($title)->hidden)
				continue;

			// rename the column
			if ($this->_column($title)->title)	
				$title = $this->_column($title)->title;

			$header .= "<th>".ucwords ($title)."</th>";
		}	

		if (count($this->_options))
			$header .= "<th>Options</th>";

		$html = "<table>{$header}";
		foreach ($resultTRs as $tr=>$line) {
			$html .= $line.implode('</td>', array_merge($resultTDs[$tr], isset($options[$tr]) ? array($options[$tr]) : array())).'</td></tr>';
		}	

		return $html.'</table>';	
	}

	/**
	* Get a column object 
	*
	* @return StdObj
	*/
	private function _column($column) {
		if (!isset($this->_columns[$column]))
			$this->_columns[$column] = new FaZend_StdObject();
		return $this->_columns[$column];
	}

	/**
	* Get a column object 
	*
	* @return StdObj
	*/
	private function _option($option) {
		if (!isset($this->_options[$option]))
			$this->_options[$option] = new FaZend_StdObject();
		return $this->_options[$option];
	}

}
