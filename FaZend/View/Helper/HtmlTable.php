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

require_once 'FaZend/View/Helper.php';

/**
 * Nice and configurable html table
 *
 * @see http://naneau.nl/2007/07/08/use-the-url-view-helper-please/
 * @package View
 * @subpackage Helper
 */
class FaZend_View_Helper_HtmlTable extends FaZend_View_Helper
{

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
    protected $_injectedColumns = array();

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
     * Separator between options in special "options" column
     *
     * @var string
     */
    protected $_optionsSeparator = '&#32;';

    /**
     * Columns to show, if undefined = show all columns
     *
     * @var string[]
     */
    protected $_columnsToShow;
    
    /**
     * List of injected variables
     *
     * @var array
     * @see setInjection()
     * @see getInjection()
     */
    protected $_injections = array();

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
    public function htmlTable($name = null)
    {
        // no name means no multi-instance - short and fast scenario
        if (is_null($name)) {
            return $this;
        }

        // initialize this particular table
        if (!isset(self::$_instances[$name])) {
            self::$_instances[$name] = clone $this;
        }

        return self::$_instances[$name];
    }

    /**
     * Show the table
     *
     * Convert all internal data into string (HTML) and return it
     *
     * @return string HTML
     */
    public function __toString()
    {
        try {
            $html = $this->_render();
        } catch (Exception $e) {
            $html = sprintf(
                'Exception %s: %s', 
                get_class($e), 
                $e->getMessage()
            );
        }

        return $html;
    }

    /**
     * Inject a variable
     *
     * @param string Name of the injection
     * @param mixed Variable to use
     * @return $this
     * @uses $this->_injections
     */
    public function setInjection($name, $variable = false) 
    {
        $this->_injections[$name] = $variable;
        return $this;
    }

    /**
     * Retrieve an injected variable
     *
     * @param string Name of the injection
     * @return mixed
     * @throws FaZend_View_Helper_HtmlTable_InjectionNotFoundException
     * @uses $this->_injections
     */
    public function getInjection($name) 
    {
        if (!isset($this->_injections[$name])) {
            FaZend_Exception::raise(
                'FaZend_View_Helper_HtmlTable_InjectionNotFoundException', 
                "Injection '{$name}' not found"
            );
        }
        return $this->_injections[$name];
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
    public function setPaginator(Zend_Paginator $paginator)
    {
        $this->_paginator = $paginator;
        return $this;
    }

    /**
     * Add converter to the column
     *
     * If FALSE provided instead of $column name, this converter will
     * try to convert the entire row, and if the result is FALSE the
     * row will be ignored
     *
     * @param string|false Column name, case sensitive, or FALSE if it relates to ROW
     * @param mixed Callback
     * @return FaZend_View_Helper_HtmlTable
     * @see FaZend_Callback
     */
    public function addConverter($column, $callback)
    {
        $this->_column($column)->converters[] = FaZend_Callback::factory($callback);
        return $this;
    }

    /**
     * Add formatter to the column (or to the row)
     *
     * @param string|false Column name, case sensitive, or FALSE if it relates to ROW
     * @param mixed Callback
     * @param string|null Style
     * @return FaZend_View_Helper_HtmlTable
     */
    public function addFormatter($column, $callback, $style = null)
    {
        $this->_column($column)->formatters[] = array(
            'condition' => FaZend_Callback::factory($callback),
            'style' => $style,
        );
        return $this;
    }

    /**
     * Hide one column
     *
     * @param string Column name, case sensitive
     * @return FaZend_View_Helper_HtmlTable
     */
    public function hideColumn($column)
    {
        $this->_column($column)->hidden = true;
        return $this;
    }

    /**
     * Add new column
     *
     * @param string Column name, case sensitive
     * @param string Column name, case sensitive, which will preceede this new column
     * @return FaZend_View_Helper_HtmlTable
     * @throws FaZend_View_Helper_HtmlTable_IllegalParameter
     */
    public function addColumn($column, $predecessor)
    {
        if ($column == $predecessor) {
            FaZend_Exception::raise(
                'FaZend_View_Helper_HtmlTable_IllegalParameter', 
                "Column '{$column}' cannot precede itself"
            );
        }

        $this->_injectedColumns[$column] = $predecessor;
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
    public function showColumns(array $columns)
    {
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
    public function appendOptionToColumn($option, $column)
    {
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
    public function addColumnLink(
        $title, 
        $httpVar, 
        $column, 
        array $urlParams, 
        $route = 'default', 
        $reset = false, 
        $encode = true
    )
    {
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
    public function addOption(
        $title, 
        $httpVar, 
        $column, 
        array $urlParams, 
        $route = 'default', 
        $reset = false, 
        $encode = true
    )
    {
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
    public function skipOption($title, $callback)
    {
        $this->_option($title)->skip = FaZend_Callback::factory($callback);
        return $this;
    }

    /**
     * Set column title
     *
     * @param string Column name, case sensitive
     * @param string Column title to be displayed
     * @return FaZend_View_Helper_HtmlTable
     */
    public function setColumnTitle($column, $title)
    {
        $this->_column($column)->title = $title;
        return $this;
    }

    /**
     * Allow raw HTML output in the column
     *
     * @param string Column name, case sensitive
     * @return FaZend_View_Helper_HtmlTable
     */
    public function allowRawHtml($column)
    {
        $this->_column($column)->rawHtml = true;
        return $this;
    }

    /**
     * Set message to show if no data
     *
     * @param string Text message to show when the paginator has no data
     * @return FaZend_View_Helper_HtmlTable
     */
    public function setNoDataMessage($msg)
    {
        $this->_noDataMessage = $msg;
        return $this;
    }

    /**
     * Render the table and return HTML
     *
     * @return string HTML table
     */
    protected function _render()
    {
        $resultTRs = array();
        $resultTDs = array();
        $options = array();

        foreach ($this->_paginator as $key=>$rowOriginal) {
            // convert ROW, if necessary. skip the ROW if false returned
            if (!$this->_convertColumnValue(false, true, $rowOriginal)) {
                continue;
            }

            // prepare one row for rendering, converting it to the ARRAY
            // type, no matter what was the original type.
            if (is_array($rowOriginal)) {
                $row = $rowOriginal;
            } elseif (is_object($rowOriginal)) {
                if (method_exists($rowOriginal, 'toArray')) {
                    $row = $rowOriginal->toArray();
                } else {
                    $row = array();
                }
            } else {
                $row = array('column'=>$rowOriginal);
            }

            // inject columns
            foreach ($this->_injectedColumns as $injectedColumn=>$predecessor) {
                // sanity check
                if (!is_object($rowOriginal)) {
                    break;
                }
                
                // if it's a method - call it
                if ($injectedColumn == '__key') {
                    $injectedValue = $key;
                } elseif (method_exists($rowOriginal, $injectedColumn)) {
                    $injectedValue = $rowOriginal->$injectedColumn();
                } else {
                    $injectedValue = $rowOriginal->$injectedColumn;
                }

                $this->_inject($row, $injectedColumn, $predecessor, $injectedValue);
            }

            $resultTRs[] = sprintf(
                "\t<tr class='%s' onmouseover='this.className=\"highlight\"' " .
                "onmouseout='this.className=\"%s\"'%s>\n",
                (fmod(count($resultTRs), 2) ? 'even' : 'odd'),
                (fmod(count($resultTRs), 2) ? 'even' : 'odd'),
                $this->_formatColumnStyle(false, null, $rowOriginal)
            );

            $tds = array();    
            foreach ($row as $title=>$value) {
                $column = $this->_column($title);

                // maybe we should show only some particular columns
                if (!$this->_isVisible($title)) {
                    continue;
                }

                // convert value, if necessary
                $value = $this->_convertColumnValue($title, $value, $rowOriginal);

                // attach link to the TD
                if ($column->link) {
                    $value = $this->_resolveLink($column->link, $value, $rowOriginal, $key);
                }

                // append CSS style
                $tds[$title] = sprintf(
                    "\t\t<td%s>%s",
                    $this->_formatColumnStyle($title, $value, $rowOriginal),
                    $value
                );
            }    

            if (count($this->_options)) {
                $optString = "\t\t<td>";
                foreach ($this->_options as $option) {
                    // skip the option
                    if ($option->skip) {
                        if ($option->skip->call($rowOriginal)) {
                            continue;
                        }
                    }    

                    // build the <A HREF> link for this option
                    $optLink = $this->_optionsSeparator . 
                    $this->_resolveLink($option->link, $option->title, $rowOriginal, $key);

                    // attach this option to the particular column    
                    if ($option->toColumn) {
                        $tds[$option->toColumn] .= ' ' . $optLink;
                    } else {
                        $optString .= $optLink;    
                    }
                }
                $options[] = $optString;    
            }    
            $resultTDs[] = $tds;
        }
        
        // if no data in the paginator
        if (!count($resultTDs)) {
            return $this->_noDataMessage;
        }

        // build the header using the last ROW information
        $header = "\t<tr><!-- header -->\n";
        foreach ($row as $title=>$value) {
            if (!$this->_isVisible($title)) {
                continue;
            }

            // rename the column
            if ($this->_column($title)->title) {
                $title = $this->_column($title)->title;
            }

            $header .= "\t\t<th>{$title}</th>\n";
        }    

        // add header column for OPTIONS
        if (count($this->_options)) {
            $header .= "\t\t<th>" . _t('Options') . "</th>\n";
        }
        $header .= "\t</tr>\n";

        $html = "\n<table>\n" . $header;
        foreach ($resultTRs as $tr=>$line) {
            $html .= 
            $line . 
            implode(
                "</td>\n", 
                array_merge(
                    $resultTDs[$tr], 
                    isset($options[$tr]) ? array($options[$tr]) : array()
                )
            ) . 
            "</td>\n\t</tr>\n";
        }    

        return $html . "</table>\n";    
    }

    /**
     * Get a column object 
     *
     * @param string Column name
     * @return StdObj
     */
    protected function _column($column)
    {
        if (!isset($this->_columns[$column])) {
            $this->_columns[$column] = FaZend_StdObject::create()
                ->set('converters', array())
                ->set('formatters', array());
        }
        return $this->_columns[$column];
    }

    /**
     * Get a column object 
     *
     * @param string Option name
     * @return StdObj
     */
    protected function _option($option)
    {
        if (!isset($this->_options[$option])) {
            $this->_options[$option] = new FaZend_StdObject();
        }
        return $this->_options[$option];
    }

    /**
     * This column is visible?
     *
     * @param string Column name
     * @return boolean
     */
    protected function _isVisible($column)
    {
        // maybe we should show only some particular columns
        if ($this->_columnsToShow) {
            if (!in_array($column, $this->_columnsToShow)) {
                return false;
            }
        }

        // skip the column
        if ($this->_column($column)->hidden) {
            return false;
        }

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
    protected function _makeLink($name, $httpVar, $column, $urlParams, $route, $reset, $encode)
    {
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
     * @param array|object Row data
     * @param string Key of the row
     * @return string HTML
     */
    protected function _resolveLink(FaZend_StdObject $link, $title, $row, $key)
    {
        $params = $link->urlParams;

        // you can specify params as callbacks
        foreach ($params as &$param) {
            if ($param instanceof FaZend_Callback) {
                $param = $param->call($link->name, $row, $key);
            }
        }

        // if this process is required - do it
        if ($link->httpVar) {
            $params += array(
                $link->httpVar => (is_object($row) ? $row->{$link->column} : $row[$link->column])
            );
        }

        return sprintf(
            "<a href='%s'>%s</a>",
            $this->getView()->url($params, $link->route, $link->reset, $link->encode),
            $title
        );
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
    protected function _inject(array &$row, $column, $predecessor, $value)
    {
        $result = array();
        if (!$predecessor) {
            $result = array_merge(array($column=>$value), $row);
        } else {
            foreach ($row as $key=>$val) {
                $result[$key] = $val;
                if ($key == $predecessor) {
                    $result[$column] = $value;
                }
            }
        }
        $row = $result;
    }

    /**
     * Convert value of the column
     *
     * @param string|false Name of the column
     * @param string Current value of the column
     * @param mixed Original row we're working with
     * @return mixed
     * @throws FaZend_View_Helper_HtmlTable_InvalidConverter
     **/
    protected function _convertColumnValue($name, $value, $row)
    {
        $column = $this->_column($name);
        foreach ($column->converters as $converter) {
            $value = $converter->call($value, $row, $this);
        }

        if ($name !== false) {
            // sanity check
            if (!is_string($value)) {
                $value = (string)$value;
            }

            // strip HTML tags    
            if (!$column->rawHtml) {
                $value = htmlspecialchars($value);
            }
        }

        return $value;
    }
    
    /**
     * Build and return style for the specified column or row
     *
     * @param string|false Name of the column or FALSE if it means ROW
     * @param string Current value of the column
     * @param mixed Original row we're working with
     * @return string
     * @throws FaZend_View_Helper_HtmlTable_InvalidFormatter
     */
    protected function _formatColumnStyle($name, $value, $row) 
    {
        $styles = array();
        foreach ($this->_column($name)->formatters as $formatter) {
            $style = $formatter['condition']->call($value, $row, $this);
            if (is_bool($style)) {
                if ($style) {
                    $styles[] = $formatter['style'];
                }
            } else {
                $styles[] = $style;
            }
        }
        return count($styles) ? ' style="' . implode(';', $styles) . '"' : '';
    }

}
