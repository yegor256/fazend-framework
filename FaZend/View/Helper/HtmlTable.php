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

require_once 'FaZend/View/Helper.php';

/**
 * Nice and configurable html table
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package FaZend 
 */
class FaZend_View_Helper_HtmlTable extends FaZend_View_Helper {

    /**
     * Paginator to be used
     *
     * @var Zend_Paginator
     */
    protected $_paginator;
    
    /**
     * List of columns defined by set..()
     *
     * @var array
     */
    protected $_columns = array();

    /**
     * List of injected columns
     *
     * @var array
     */
    protected $_injections = array();

    /**
     * List of options defined by set..()
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Message to show if no data
     *
     * @var strint
     */
    protected $_noDataMessage = 'no data';

    /**
     * Columns to show, if undefined = show all columns
     *
     * @var string[]
     */
    protected $_columnsToShow;

    /**
     * Instances of this class, in case we nee many tables in one page
     *
     * @var FaZend_View_Helper_HtmlTable[]
     */
    protected static $_instances = array();

    /**
     * Show the table
     *
     * @return string HTML
     */
    public function htmlTable($name = null) {

        // no name means no multi-instance - short and fast scenario
        if (is_null($name))
            return $this;

        // initialize this particular table
        if (!isset(self::$_instances[$name]))
            self::$_instances[$name] = clone $this;

        return self::$_instances[$name];

    }

    /**
     * Show the table
     *
     * Convert all internal data into string (HTML) and return it
     *
     * @return string HTML
     */
    public function __toString() {
        
        try {
            $html = $this->_render();
        } catch (Exception $e) {
            $html = 'Exception: ' . $e->getMessage();
        }

        return $html;
            
    }

    /**
     * Set the paginator with the data
     *
     * Saves data source into helper, to be rendered. Without this
     * method the helper won't display anything
     *
     * @param Zend_Paginator Data holder to render
     * @return FaZend_View_Helper_HtmlTable
     */
    public function setPaginator(Zend_Paginator $paginator) {
        $this->_paginator = $paginator;
        return $this;
    }

    /**
     * Set parser for a given column
     *
     * @param string Column name, case sensitive
     * @param callback Function to be called with each row
     * @return FaZend_View_Helper_HtmlTable
     */
    public function setParser($column, $callback) {
        $this->_column($column)->parser = $callback;
        return $this;
    }

    /**
     * Set helper for a given column
     *
     * @param string Column name, case sensitive
     * @param string Name of the view helper to apply
     * @return FaZend_View_Helper_HtmlTable
     */
    public function setParserHelper($column, $helper) {
        $this->_column($column)->helper = $helper;
        return $this;
    }

    /**
     * Hide one column
     *
     * @param string Column name, case sensitive
     * @return FaZend_View_Helper_HtmlTable
     */
    public function hideColumn($column) {
        $this->_column($column)->hidden = true;
        return $this;
    }

    /**
     * Ask this helper to calculate sum of this column
     *
     * @param string Column name, case sensitive
     * @return FaZend_View_Helper_HtmlTable
     */
    public function calculateSum($column) {
        $this->_column($column)->sum = true;
        return $this;
    }

    /**
     * Returns calculated sum for this column
     *
     * @param string Column name, case sensitive
     * @return FaZend_View_Helper_HtmlTable
     */
    public function getSum($column) {
        return $this->_column($column)->sumValue;
    }

    /**
     * Add new column
     *
     * @param string Column name, case sensitive
     * @param string Column name, case sensitive, which will preceede this new column
     * @return FaZend_View_Helper_HtmlTable
     */
    public function addColumn($column, $predecessor) {

        if ($column == $predecessor) {
            FaZend_Exception::raise('FaZend_View_Helper_HtmlTable_IllegalParameter', 
                'Column cannot precede itself');
        }

        $this->_injections[$column] = $predecessor;
        return $this;
    }

    /**
     * Indicate a list of columns that should be visible
     *
     * This method could be used instead of hideColumn()
     *
     * @param array List of columns to show
     * @return FaZend_View_Helper_HtmlTable
     */
    public function showColumns(array $columns) {
        $this->_columnsToShow = $columns;
        return $this;
    }

    /**
     * Append option to column, instead of 'options' column
     *
     * @param string Option name, case sensitive
     * @param string Column name, case sensitive
     * @return FaZend_View_Helper_HtmlTable
     */
    public function appendOptionToColumn($option, $column) {
        $this->_option($option)->toColumn = $column;
        return $this;
    }

    /**
     * Add column link
     *
     * @param string Name of the column to attach to
     * @param string HTTP variable to pass to the link
     * @param string What column value to use for this HTTP var
     * @param array Other URL params
     * @param string Route to use in URL
     * @return FaZend_View_Helper_HtmlTable
     */
    public function addColumnLink($title, $httpVar, $column, array $urlParams, $route = 'default', $reset = false, $encode = true) {
        $this->_column($title)->link = $this->_makeLink($title, $httpVar, $column, $urlParams, $route, $reset, $encode);
        return $this;
    }

    /**
     * Add option
     *
     * @param string Option name, case sensitive
     * @param string Http variable to be used in links, in params
     * @param string Source of data for the Http variable, column name
     * @param array Array of parameters for url()
     * @return FaZend_View_Helper_HtmlTable
     */
    public function addOption($title, $httpVar, $column, array $urlParams, $route = 'default', $reset = false, $encode = true) {
        $this->_option($title)->title = $title;
        $this->_option($title)->link = $this->_makeLink($title, $httpVar, $column, $urlParams, $route, $reset, $encode);
        return $this;
    }

    /**
     * Conditional skip of the option
     *
     * @param string Option name, case sensitive
     * @param callback Function to be called to understand when the option should be skipped
     * @return FaZend_View_Helper_HtmlTable
     */
    public function skipOption($title, $callback) {
        $this->_option($title)->skip = $callback;
        return $this;
    }

    /**
     * Add column style
     *
     * @param string Column name, case sensitive
     * @param string CSS style
     * @return FaZend_View_Helper_HtmlTable
     */
    public function addColumnStyle($column, $style) {
        $this->_column($column)->style = $style;
        return $this;
    }

    /**
     * Set column title
     *
     * @param string Column name, case sensitive
     * @param string Column title to be displayed
     * @return FaZend_View_Helper_HtmlTable
     */
    public function setColumnTitle($column, $title) {
        $this->_column($column)->title = $title;
        return $this;
    }

    /**
     * Allow raw HTML output in the column
     *
     * @param string Column name, case sensitive
     * @return FaZend_View_Helper_HtmlTable
     */
    public function allowRawHtml($column) {
        $this->_column($column)->rawHtml = true;
        return $this;
    }

    /**
     * Set message to show if no data
     *
     * @param string Text message to show when the paginator has no data
     * @return FaZend_View_Helper_HtmlTable
     */
    public function setNoDataMessage($msg) {
        $this->_noDataMessage = $msg;
        return $this;
    }

    /**
     * Render the table
     *
     * @return string HTML table
     */
    protected function _render() {

        // if no data in the paginator
        if (!count($this->_paginator))
            return $this->_noDataMessage;

        $resultTRs = array();
        $resultTDs = array();
        $options = array();

        foreach ($this->_paginator as $key=>$rowOriginal) {

            if (!is_array($rowOriginal))
                $row = $rowOriginal->toArray();
            else
                $row = $rowOriginal;

            // inject columns
            // predecessor is ignored so far
            // @todo implement it properly
            foreach ($this->_injections as $injectedColumn=>$predecessor) {
                // sanity check
                if (!is_object($rowOriginal))
                    break;
                
                // if it's a method - call it
                if ($injectedColumn == '__key')
                    $injectedValue = $key;
                else if (method_exists($rowOriginal, $injectedColumn))
                    $injectedValue = $rowOriginal->$injectedColumn();
                else
                    $injectedValue = $rowOriginal->$injectedColumn;

                $this->_inject($row, $injectedColumn, $predecessor, $injectedValue);
            }

            $resultTRs[] = "<tr class='".(fmod (count($resultTRs), 2) ? 'even' : 'odd').
                "' onmouseover='this.className=\"highlight\"' onmouseout='this.className=\"".
                (fmod(count($resultTRs), 2) ? 'even' : 'odd')."\"'>";

            $tds = array();    
            foreach ($row as $title=>$value) {

                // summarize column values
                if ($this->_column($title)->sum)
                    $this->_column($title)->sumValue += $value;

                // maybe we should show only some particular columns
                if (!$this->_isVisible($title))
                    continue;

                // parse the value of this TD    
                if ($this->_column($title)->parser) {
                    $parser = $this->_column($title)->parser;
                    $value = $parser($value, $rowOriginal);
                }    

                // parse the value of this TD with helper
                if ($this->_column($title)->helper) {
                    $helper = $this->_column($title)->helper;
                    $value = $this->getView()->$helper($value);
                }

                // sanity check
                if (!is_string($value))
                    $value = (string)$value;

                // strip HTML tags    
                if (!$this->_column($title)->rawHtml)
                    $value = htmlspecialchars($value);

                // attach link to the TD
                if ($this->_column($title)->link)
                    $value = $this->_resolveLink($this->_column($title)->link, $value, $row);

                // append CSS style
                $tds[$title] = '<td' . ($this->_column($title)->style ? " style='{$this->_column($title)->style}'" : false) .
                    '>' . $value;
            }    

            if (count($this->_options)) {
                $optString = '<td>';
                foreach ($this->_options as $option) {

                    // skip the option
                    if ($option->skip) {
                        $skip = $option->skip;
                        if ($skip($rowOriginal))
                            continue;
                    }    

                    // build the <A HREF> link for this option
                    $optLink = '&#32;' . $this->_resolveLink($option->link, $option->title, $row, $key);

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
            if (!$this->_isVisible($title))
                continue;

            // rename the column
            if ($this->_column($title)->title)    
                $title = $this->_column($title)->title;

            $header .= '<th>' . ucwords($title) . '</th>';
        }    

        if (count($this->_options))
           $header .= '<th>Options</th>';

        $html = '<table>' . $header;
        foreach ($resultTRs as $tr=>$line) {
            $html .= $line . 
                implode('</td>', array_merge($resultTDs[$tr], isset($options[$tr]) ? array($options[$tr]) : array())) .
                '</td></tr>';
        }    

        return $html . '</table>';    
    }

    /**
     * Get a column object 
     *
     * @param string Column name
     * @return StdObj
     */
    protected function _column($column) {
        if (!isset($this->_columns[$column]))
            $this->_columns[$column] = new FaZend_StdObject();
        return $this->_columns[$column];
    }

    /**
     * Get a column object 
     *
     * @param string Option name
     * @return StdObj
     */
    protected function _option($option) {
        if (!isset($this->_options[$option]))
            $this->_options[$option] = new FaZend_StdObject();
        return $this->_options[$option];
    }

    /**
     * This column is visible?
     *
     * @param string Column name
     * @return boolean
     */
    protected function _isVisible($column) {

        // maybe we should show only some particular columns
        if ($this->_columnsToShow) {
            if (!in_array($column, $this->_columnsToShow))
                return false;
        }

        // skip the column
        if ($this->_column($column)->hidden)
            return false;

        return true;

    }

    /**
     * Create new LINK object to save into option or into column
     *
     * @param string Unique name of this link (link name or column name)
     * @param string Name of HTTP parameter
     * @param string Column name to be used as param
     * @param array Associative array of params
     * @param string Name of route
     */
    protected function _makeLink($name, $httpVar, $column, $urlParams, $route, $reset, $encode) {
        $link = new FaZend_StdObject();
        $link->httpVar = $httpVar;
        $link->urlParams = $urlParams;
        $link->column = $column;
        $link->route = $route;
        $link->name = $name;
        $link->reset = $reset;
        $link->encode = $encode;
        return $link;
    }

    /**
     * Resolve link object into HTML link
     *
     * @param FaZend_StdObject Link object
     * @param string Text to show, previously escaped, if necessary
     * @param array Row data
     * @param string Key of the row
     * @return string HTML
     */
    protected function _resolveLink(FaZend_StdObject $link, $title, array $row, $key) {
        $params = $link->urlParams;

        // you can specify params as callbacks
        foreach ($params as &$param) {
            if (is_callable($param)) {
                if (is_array($param))
                    $param = call_user_func_array($param, array(
                        'name'=>$link->name,
                        'row'=>$row,
                        'key'=>$key));
                else
                    $param = $param($link->name, $row);
            }
        }

        // if this process is required - do it
        if ($link->httpVar)
            $params += array($link->httpVar => $row[$link->column]);

        return "<a href='".$this->getView()->url($params, $link->route, $link->reset, $link->encode)."'>" . $title . '</a>';
    }

    /**
     * Inject one column into a row
     *
     * @param array Row to manage
     * @param string Name of column to be inserted
     * @param string Name of predecessor
     * @param string The value to be insterted
     * @return void
     */
    protected function _inject(array &$row, $column, $predecessor, $value) {
        $result = array();

        if (!$predecessor)
            $result = array_merge(array($column=>$value), $row);
        else {
            while (current($row)) {
                $result[key($row)] = current($row);
                if (key($row) == $predecessor)
                    $result[$column] = $value;
                next($row);
            }
        }

        $row = $result;
    }

}
