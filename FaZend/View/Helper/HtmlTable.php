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
     * @param Zend_Paginator Data holder to render
     * @return HtmlTable
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
     * @return HtmlTable
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
     * @return HtmlTable
     */
    public function setParserHelper($column, $helper) {
        $this->_column($column)->helper = $helper;
        return $this;
    }

    /**
     * Hide one column
     *
     * @param string Column name, case sensitive
     * @return HtmlTable
     */
    public function hideColumn($column) {
        $this->_column($column)->hidden = true;
        return $this;
    }

    /**
     * Ask this helper to calculate sum of this column
     *
     * @param string Column name, case sensitive
     * @return HtmlTable
     */
    public function calculateSum($column) {
        $this->_column($column)->sum = true;
        return $this;
    }

    /**
     * Returns calculated sum for this column
     *
     * @param string Column name, case sensitive
     * @return HtmlTable
     */
    public function getSum($column) {
        return $this->_column($column)->sumValue;
    }

    /**
     * Add new column
     *
     * @param string Column name, case sensitive
     * @param string Column name, case sensitive, which will preceede this new column
     * @return HtmlTable
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
     * Append option to column, instead of 'options' column
     *
     * @param string Option name, case sensitive
     * @param string Column name, case sensitive
     * @return HtmlTable
     */
    public function appendOptionToColumn($option, $column) {
        $this->_option($option)->toColumn = $column;
        return $this;
    }

    /**
     * Add option
     *
     * @param string Option name, case sensitive
     * @param string Http variable to be used in links, in params
     * @param string Source of data for the Http variable, column name
     * @param array Array of parameters for url()
     * @return HtmlTable
     */
    public function addOption($title, $httpVar, $column, array $urlParams) {
        $this->_option($title)->title = $title;
        $this->_option($title)->httpVar = $httpVar;
        $this->_option($title)->column = $column;
        $this->_option($title)->urlParams = $urlParams;
        return $this;
    }

    /**
     * Conditional skip of the option
     *
     * @param string Option name, case sensitive
     * @param callback Function to be called to understand when the option should be skipped
     * @return HtmlTable
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
     * @return HtmlTable
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
     * @return HtmlTable
     */
    public function setColumnTitle($column, $title) {
        $this->_column($column)->title = $title;
        return $this;
    }

    /**
     * Allow raw HTML output in the column
     *
     * @param string Column name, case sensitive
     * @return HtmlTable
     */
    public function allowRawHtml($column) {
        $this->_column($column)->rawHtml = true;
        return $this;
    }

    /**
     * Set message to show if no data
     *
     * @param string Text message to show when the paginator has no data
     * @return HtmlTable
     */
    public function setNoDataMessage($msg) {
        $this->_noDataMessage = $msg;
        return $this;
    }

    /**
     * Add column link
     *
     * @param string Name of the column to attach to
     * @param string HTTP variable to pass to the link
     * @param string What column value to use for this HTTP var
     * @param array Other URL params
     * @return HtmlTable
     */
    public function addColumnLink($title, $httpVar, $column, array $urlParams) {
        
        $link = new FaZend_StdObject();
        $link->httpVar = $httpVar;
        $link->urlParams = $urlParams;
        $link->column = $column;
        $this->_column($title)->link = $link;
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

        foreach ($this->_paginator as $rowOriginal) {

            if (!is_array($rowOriginal))
                $row = $rowOriginal->toArray();
            else
                $row = $rowOriginal;

            // inject columns
            foreach ($this->_injections as $injectedColumn=>$predecessor) {
                // sanity check
                if (!is_object($rowOriginal))
                    break;
                
                // if it's a method - call it
                if (method_exists($rowOriginal, $injectedColumn))
                    $row[$injectedColumn] = $rowOriginal->$injectedColumn();
                else
                    $row[$injectedColumn] = $rowOriginal->$injectedColumn;
            }

            $resultTRs[] = "<tr class='".(fmod (count($resultTRs), 2) ? 'even' : 'odd').
                "' onmouseover='this.className=\"highlight\"' onmouseout='this.className=\"".
                (fmod(count($resultTRs), 2) ? 'even' : 'odd')."\"'>";

            $tds = array();    
            foreach ($row as $title=>$value) {

                // summarize column values
                if ($this->_column($title)->sum)
                    $this->_column($title)->sumValue += $value;

                // skip this column if required
                if ($this->_column($title)->hidden)
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
                if ($this->_column($title)->link) {
                    $link = $this->_column($title)->link;
                    $value = "<a href='".$this->getView()->url($link->urlParams + 
                        array($link->httpVar => $row[$link->column]), 'default', true)."'>" . $value . '</a>';
                }    

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
                    $optLink = "&#32;<a href='".$this->getView()->url(
                        $option->urlParams + array($option->httpVar => $row[$option->column]), 'default', true).
                        "'>" . $option->title . '</a>';

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

}
