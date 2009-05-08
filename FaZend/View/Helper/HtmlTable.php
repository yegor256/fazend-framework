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

	private $_view;
	private $_paginator;
	private $_parsers = array();
	private $_hidden = array();
	private $_options = array();
	private $_styles = array();
	private $_titles = array();
	private $_links = array();

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
		$this->_parsers[$column] = $func;
		return $this;
	}

	/**
	* Hide one column
	*
	* @return HtmlTable
	*/
	public function hideColumn($column) {
		$this->_hidden[$column] = true;
		return $this;
	}

	/**
	* Append option to column, instead of 'options' column
	*
	* @return HtmlTable
	*/
	public function appendOptionToColumn($option, $column) {
		$this->_options[$option]['toColumn'] = $column;
		return $this;
	}

	/**
	* Add option
	*
	* @return HtmlTable
	*/
	public function addOption($title, $httpVar, $column, $urlParams) {
		$this->_options[$title] = array(
			'title'=>$title, 
			'httpVar'=>$httpVar, 
			'column'=>$column, 
			'urlParams'=>$urlParams);
		return $this;
	}

	/**
	* Conditional skip of the option
	*
	* @return HtmlTable
	*/
	public function skipOption($title, $func) {
		$this->_options[$title]['skip'] = $func;
		return $this;
	}

	/**
	* Add column style
	*
	* @return HtmlTable
	*/
	public function addColumnStyle($column, $style) {
		$this->_styles[$column] = $style;
		return $this;
	}

	/**
	* Set column title
	*
	* @return HtmlTable
	*/
	public function setColumnTitle($column, $title) {
		$this->_titles[$column] = $title;
		return $this;
	}

	/**
	* Add column link
	*
	* @return HtmlTable
	*/
	public function addColumnLink($title, $httpVar, $column, $urlParams) {
		$this->_links[$title] = array(
			'title'=>$title, 
			'httpVar'=>$httpVar, 
			'column'=>$column, 
			'urlParams'=>$urlParams);
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

				if (isset($this->_hidden[$title]))
					continue;

				$value = htmlspecialchars($value);

				if (isset($this->_parsers[$title]))
					$value = $this->_parsers[$title] ($value, $rowOriginal);

				if (isset($this->_links[$title]))	
					$value = "<a href='".$this->getView()->url($this->_links[$title]['urlParams'] + 
						array($this->_links[$title]['httpVar']=>$row[$this->_links[$title]['column']]), 'default', true)."'>{$value}</a>";

				$tds[$title] = "<td".(isset($this->_styles[$title]) ? " style='{$this->_styles[$title]}'" : false).">{$value}";
			}	

			if (count($this->_options)) {
				$optString = '<td>';
				foreach ($this->_options as $opt) {

					if (!empty($opt['skip'])) {
						if ($opt['skip'] ($rowOriginal))
							continue;
					}	

					$optLink = "&#32;<a href='".$this->getView()->url(
						$opt['urlParams'] + array($opt['httpVar']=>$row[$opt['column']]), 'default', true).
						"'>{$opt['title']}</a>";

					if (isset($opt['toColumn']))	
						$tds[$opt['toColumn']] .= $optLink;
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
			if (isset($this->_hidden[$title]))
				continue;

			// rename the column
			if (isset($this->_titles[$title]))	
				$title = $this->_titles[$title];

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

}
