<?php

/*
 * ====================================================================
 *
 * License:      GNU General Public License
 *
 * Copyright (c) 2007 Centare Group Ltd.  All rights reserved.
 *
 * This file is part of PHP Lite Framework
 *
 * PHP Lite Framework is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2.1
 * of the License, or (at your option) any later version.
 *
 * PHP Lite Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Please refer to the file license.txt in the root directory of this
 * distribution for the GNU General Public License or see
 * http://www.gnu.org/licenses/lgpl.html
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * ====================================================================
 *
 */

class PLF_Table
{

  var $tableAttributes;
  var $rows;
  var $alternating;
  var $doAlternating = false;
  var $sortableTable = false;
  var $entriesPerPage = null;
  var $headingCSSClass;
  var $evenRowCSSClass = 'tableEvenRow';
  var $oddRowCSSClass = 'tableOddRow';
  var $doColumnStyling = false;
  var $columnCSSClasses;
  var $columnCSSStyles;
  var $caption;
  var $captionCSSClass;
  var $heading;
  var $numCells;
  var $invisibleIfEmpty;
  var $rowsArray = array();
  var $tableId;
  var $editable = false; // whether we are doing ajax editing
  var $editableColumnConfig = array(); // see setEditable
  var $primaryKeyFieldname; // see setEditable
  var $url; // see setEditable
  var $callbackDivName; // see setEditable
  var $searchable;

  // construct a table
  // $heading can be a series of strings to be used as the heading of the table
  // or an array of strings
  function __construct($heading = null) {
    static $counter;
    $this->tableId = "table".$counter = $counter + 1;
    $this->heading = $heading;
    $this->numCells = count($heading); // save off count for the spacer
  }

  function PLF_Table($heading) {
    self::__construct($heading);
  }

  // set the attributes of the table
  // ex: setAttributes('width="100%" border="0"');
  function setAttributes($attributes) {
    $this->tableAttributes = $attributes;
  }

  function setInvisibleIfEmpty() {
    $this->invisibleIfEmpty = true;
  }

  /**
   * Specifies that rows in the table will be css styled
   * to achive different colors for alternating rows.
   *
   * To specify the specific css classname to use for alternating
   * rows, use:
   *
   * setOddRowsCSSClass()
   * and
   * setEvenRowsCSSClass()
   *
   * If you don't call these, the css classnames will default
   * to "tableOddRow" and "tableEvenRow", respectively.
   */
  function setAlternateRowStyles() {
    $this->doAlternating = TRUE;
  }

  /**
   * Set whether to do column styling.
   * Column CSS classnames can be set using
   * setColumnCSSClasses() or setColumCSSSTyles()
   * or will default to "col1", "col2" etc.
   *
   * Styles can then be set on columns as in the following CSS example:
   *
   * table.tableCSSClassName td.col1 {
   *   text-align: right;
   * }
   */
  function setDoColumnStyling($bool = TRUE) {

    $this->doColumnStyling = $bool;
  }

  /**
   *  Takes an array or multiple arguments to set
   *  an array of names used for the CSS class names
   *  applied to the <td> elements of the table by column,
   *  if not set the default is for columns to have class names
   *  "col1" , "col2" etc.
   * ex:
   *
   *
   * $table->setDoColumnStyling();
   * // set the 5th and 6th columns with a certain alignment:
   * $table->setColumnCSSClasses(null, null, null, null, 'alignRight', 'alignCenter');
   *    */
  function setColumnCSSClasses($columnClassArray) {
    if (!is_array($columnClassArray)) {
      $columnClassArray = func_get_args();
    }
    $this->columnCSSClasses = $columnClassArray;
  }

  /**
   *  Takes an array or multiple arguments to set
   *  an array of styles used for the CSS style attribute
   *  applied to the <td> elements of the table by column,
   *
   * ex:
   *
   *
   * $table->setDoColumnStyling();
   * // set the 5th and 6th columns with a certain alignment:
   * $table->setColumnCSSStyles(null, null, null, null, 'text-align: right;', 'text-align: center;');
   */
  function setColumnCSSStyles($columnClassArray) {
    if (!is_array($columnClassArray)) {
      $columnClassArray = func_get_args();
    }
    $this->columnCSSStyles = $columnClassArray;
    if ($this->numCells != count($columnClassArray)) {
      logWarning("---- ----- WARNING ---- ---- <br/>table->setColumnCSSStyles: number of styles must match number of columns in the table!<br/>Your table header (".count($this->heading)." items): ".implode(',', $this->heading)."<br/>Your styles (".count($this->columnCSSStyles)." items): ".implode(',', $this->columnCSSStyles));
    }
  }

  /**
   * Sets a CSSClass name for the table caption element.
   * Only really needed if you want multiple tables of the same class
   * to have different caption formats.
   * Otherwise a CSS selector like "table.tableCSSClassName caption"
   * provides specificity.
   */
  function setCaptionCSSClass($captionCSSClassName) {
    $this->captionCSSClass = $captionCSSClassName;
  }

  /**
   * Sets a caption for the table.
   * As a convenience takes an optional second argument to
   * set the caption CSS class name in the one function call.
   * Otherwise the caption CSS class name can be set by a seperate call to
   * setCaptionCSSClass($className)
   */
  function setCaption($caption, $captionCSSClassName = null) {
    if (isset($captionCSSClassName)) {
      $this->setCaptionCSSClass($captionCSSClassName);
    }

    if (isset($this->captionCSSClass)) {
      $this->caption = '<caption class="'.$this->captionCSSClass.'">'.$caption.'</caption>';
    }
    else {
      $this->caption = '<caption>'.$caption.'</caption>';
    }
  }

  /**
   * Set the desired css classname to use for the
   * heading row of the table.  Leave blank to default
   * to "tableHeading"
   */
  function setHeadingCSSClass($className = 'tableHeading') {
    $this->headingCSSClass = $className;
  }

  /**
   * Set the css class name for even rows of the table
   *
   * Must call setAlternateRowStyles() to activate
   *
   * Defaults to tableEvenRow so if you just want to use
   * that classname, don't bother calling this method
   */
  function setEvenRowsCSSClass($className) {
    $this->evenRowCSSClass = $className;
  }

  /**
   * Set the css class name for odd rows of the table
   *
   * Must call setAlternateRowStyles() to activate
   *
   * Defaults to tableOddRow so if you just want to use
   * that classname, don't bother calling this method
   */
  function setOddRowsCSSClass($className) {
    $this->oddRowCSSClass = $className;
  }

  function addSpacer($value = '&nbsp;') {
    $this->rows .= '<tr>';
    for ($i=0; $i < $this->numCells; $i++) {
      $this->rows .= '<td style="text-align:center">'.$value.'</td>';
    }
    $this->rows .= '</tr>';

  }

  /**
   *  Add a row to the table.
   *
   *  row can be a series of values, or a single array holding
   *  multiple values - each value will be a cell in the row
   *  NOTE: if setEditable has been called, the first parameter MUST
   *  be the primary key of that row for the ajax edits.
   *
   */
  function addRow($row = null) {
    if ($this->doAlternating) {
      $this->alternating = !($this->alternating);
      if ($this->alternating) {
        $this->rows .= '<tr class="'.$this->oddRowCSSClass.'">';
      }
      else {
        $this->rows .= '<tr class="'.$this->evenRowCSSClass.'">';
      }
    }
    else {
      $this->rows .= '<tr>';
    }

    if (is_array($row)) {
      $rowArray = $row;
    }
    else {
      $rowArray = func_get_args();
    }

    // now pop off the first value in the rowArray assuming it will be the Primary Key
    // if setEditable has been called (this is used as part of the divid for the cell,
    // so that the ajax method will know what the primary key is of the edited cell)
    if ($this->editable) {
      $primaryKey = array_shift($rowArray);
      if (!is_numeric($primaryKey)) {
        logError("Since you called setEditable on the table, you must pass a numeric primary key as the first element in each row");
      }
    }

    $difference = count($rowArray) - count($this->heading);
    // if there aren't enough fields in the heading, add blank placeholders
    // (if we don't do this, the javascript sortable table stuff won't work)
    for ($i = 0; $i < $difference; $i++) {
      $this->heading[] = '';
    }

    // also save off the row array for the csv output
    // note, addSpacer does not do this, so we don't have spacers
    // in csv output.
    $this->rowsArray[] = $rowArray;

    $columnNumber = 0;
    foreach ($rowArray as $element) {
      $columnNumber++;
      $editableCol = false;
//      if ($this->editable && in_array($columnNumber, $this->editableColumnNumbers)) {
      if ($this->editable && isset($this->editableColumnConfig[$columnNumber])) {
        $editableCol = true;
      }
      if ($this->doColumnStyling) {
        $rowArrayCount = count($rowArray);
        if (isset($this->columnCSSClasses)) {
          $cssClassCount = count($this->columnCSSClasses);
          if ($cssClassCount != $rowArrayCount) {
            logWarning("---- ----- WARNING ---- ---- <br/>table->addRow: number of css classes must match number of columns added to a row of the table!<br/>Your addRow call contains $rowArrayCount items, yet you set up $cssClassCount css classes. Until this is fixed, you can expect other warnings/notices to accumulate");
          }
          // the array will be zero indexed, but column default class names will be indexed from 1
          $currentClass = $this->columnCSSClasses[$columnNumber - 1];
          if (isReallySet($currentClass)) {
            $tdTag = '<td class="'.$currentClass.'">';
          }
          else {
            $tdTag = '<td>';
          }
        }
        elseif (isset($this->columnCSSStyles)) {
          $cssStyleCount = count($this->columnCSSStyles);
          if ($cssStyleCount != $rowArrayCount) {
            logWarning("---- ----- WARNING ---- ---- <br/>table->addRow: number of styles must match number of columns added to a row of the table!<br/>Your addRow call contains $rowArrayCount items, yet you set up $cssStyleCount css styles. Until this is fixed, you can expect other warnings/notices to accumulate");
          }

          // the array will be zero indexed, but column default class names will be indexed from 1
          $currentStyle = $this->columnCSSStyles[$columnNumber - 1];
          if (isReallySet($currentStyle)) {
            $tdTag = '<td style="'.$currentStyle.'">';
          }
          else {
            $tdTag = '<td>';
          }
        }
        else {
          $tdTag = '<td class="col'.$columnNumber.'">';
        }
      }
      else {
        $tdTag = '<td>';
      }
      $this->rows .= $tdTag;
      if ($editableCol) {
        // put in the focus out javascript handlers and make a contentEditable cell
        // so the user can edit in place and when leaving the cell, it will call to
        // the backend via ajax to send down the edited text.
        //  See setEditable method for more explanation.
        $divid = $primaryKey.'|'.$this->editableColumnConfig[$columnNumber]['name'];
        $type = $this->editableColumnConfig[$columnNumber]['type'];
        if ('text' == $type) {
          $this->rows .= "<div id=$divid style='white-space: pre-wrap;' onclick=\"this.style.border = '1px dashed black';\" onfocusout=\"this.style.border='1px solid black';ajaxTableEdit('".jsEscapeString($this->url)."', '".$this->callbackDivName."', '".$this->primaryKeyFieldname."', '".$primaryKey."', '".$this->editableColumnConfig[$columnNumber]['name']."', this)\" contentEditable>";
          $this->rows .= $element;
          $this->rows .= '</div>';
        }
        elseif ('checkbox' == $type) {
          $checked = ($element == CHECKBOX_CHECKED || $element == CHECKBOX_CHECKED_DISPLAY);
          $this->rows .= "<input autocomplete='off' type=\"checkbox\" id=$divid onclick=\"ajaxTableEditCheckbox('".jsEscapeString($this->url)."', '".$this->callbackDivName."', '".$this->primaryKeyFieldname."', '".$primaryKey."', '".$this->editableColumnConfig[$columnNumber]['name']."', this)\" ".($checked ? 'checked="yes"' : '')."/>";
        }
        elseif ('select' == $type) {
          $this->rows .= " <select autocomplete='off' id=$divid onchange=\"ajaxTableEditSelect('".jsEscapeString($this->url)."', '".$this->callbackDivName."', '".$this->primaryKeyFieldname."', '".$primaryKey."', '".$this->editableColumnConfig[$columnNumber]['name']."', this)\" >";
          $this->rows .= "<option value=\"\"></option>";
          foreach ($this->editableColumnConfig[$columnNumber]['values'] as $key => $value) {
            $this->rows .= '<option value="'.$key.'"';
            if ($element == $key) {
              $this->rows .= ' selected ="selected"';
            }
            $this->rows .= '>'.htmlspecialchars($value);
            $this->rows .= '</option>';
          }
          $this->rows .= '</select>';

        }
      }
      else {
        $this->rows .= $element;
      }
      $this->rows .= '</td>';
    }
    $this->numCells = count($rowArray); // save off count for the spacer
    $this->rows .= '</tr>';
  }

  /**
   * Return a csv representation of the table. This function will set
   * the headers appropriately for a spreadsheet content type so that
   * the browser will interpret the stream correctly.
   *
   * $withHeader indicates if the header of the table should
   * be output as the first row in the csv stream
   * $fieldSeparator the desired field separator, (defaults to comma)
   * $lineSeparator the desired line separator, (defaults to \n)
   * $filename the desired filename that will be defaulted if user chooses
   * to save the output
   */
  function toCSV($withHeader = true, $fieldSeparator = ',', $lineSeparator = "\n", $filename = 'output.csv', $quoteFields = true) {
    header("Content-type: application/vnd.ms-excel");
    header("Content-disposition: attachment; filename=$filename");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    $toReturn = '';
    if ($withHeader) {
      if ($quoteFields) {
        // to work with php4,
        // can't use ($row as &$field) (this is only avail in php5)
        // so have to use $row[$key] = to change the current array values
        foreach ($this->heading as $key => $field) {
          $this->heading[$key] = '"'.str_replace('"', '""', $field).'"';
        }
      }
      $toReturn .= implode($fieldSeparator, $this->heading);
      $toReturn .= $lineSeparator;
    }

    foreach ($this->rowsArray as $row) {
      if ($quoteFields) {
        // to work with php4,
        // can't use ($row as &$field) (this is only avail in php5)
        // so have to use $row[$key] = to change the current array values
        foreach ($row as $key => $field) {
          $row[$key] = '"'.str_replace('"', '""', $field ?? '').'"';
        }
      }
      $toReturn .= implode($fieldSeparator, $row);
      $toReturn .= $lineSeparator;
    }
    return $toReturn;
  }

  /**
   * Return an Excel representation of the table. This function will set
   * the headers appropriately for a spreadsheet content type so that
   * the browser will interpret the stream correctly.
   *
   * $withHeader indicates if the header of the table should
   * be output as the first row in the csv stream
   * $fieldSeparator the desired field separator, (defaults to comma)
   * $lineSeparator the desired line separator, (defaults to \n)
   * $filename the desired filename that will be defaulted if user chooses
   * to save the output
   */
  function toXLS($withHeader = true, $autoSizeColumns = true, $filename = 'output.xlsx') {
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");

    if ($withHeader) {
      $rows[] = $this->heading;
      $rows = array_merge($rows, $this->rowsArray);
    }
    else {
      $rows = $this->rowsArray;
    }

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($rows);
    if ($autoSizeColumns) {
      foreach ($sheet->getColumnIterator() as $column) {
        $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
      }
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
  }


  /*
   * Set this table to be sortable, pagable, and optionally, searchable based on tinytable http://www.scriptiny.com/2009/11/advanced-javascript-table-sorter
   * Update 2021, this javascript table sorter thingy seems to be offline
   * $searchable - set to true to enable a filter search box for live searching of the table
   * $entriesPerPage - how many rows to show per page - set to 0 to disable pagination
   */

  function setFancy($searchable = true, $entriesPerPage = 50) {
    $this->sortableTable = TRUE;
    $this->searchable = $searchable;
    $this->entriesPerPage = $entriesPerPage;
  }

  /**
   * Calling this function makes the table editable via ajax.  Not all columns must be editable, see $editableColumns.  The $url provided can usually be the url to the form handling function, as long as it handles the form states of AJAX_VALID and AJAX_INVALID.
   * example:
   * $editableColumnConfig = array();
   * $editableColumnConfig[] = array('col'=>4, 'name'=>'start_date', 'type'=>'text');
   * $editableColumnConfig[] = array('col'=>5, 'name'=>'end_date', 'type'=>'text');
   * $editableColumnConfig[] = array('col'=>8, 'name'=>'sss_notes', 'type'=>'text');
   * $table->setEditable($editableColumnConfig, makeUrl('admin', 'updateChecklistTask'), 'checklist_task_id', 'tableMsg');
   *
   * @param $editableColumnConfig an array containing the column numbers and their corresponding database column names, and data types  currently we handle text
   * @param $url the ajaxy url to call when any of the editable columns has been edited (usually this can be the url to the function that provides the form handling
   * @param $primaryKeyFieldname the database field name of the primary key of the table we're displaying data from. Will be used to build the ajax call
   * @param $callbackDivName a div id that will receive whatever message is returned from the $url, used to give a notice to the user about valid edits or validation issues
   *
   */
  function setEditable($editableColumnConfig, $url, $primaryKeyFieldname, $callbackDivName) {
    $this->editable = true;

    $this->editableColumnConfig = arrayExtractFieldAsKey($editableColumnConfig, 'col');
    $this->primaryKeyFieldname = $primaryKeyFieldname;
    $this->url = $url;
    $this->callbackDivName = $callbackDivName;
  }

  function getTablenav() {
    $frameworkUrl = getFrameworkUrl();
    return '<div class="tinytable-tablenav" id="'.$this->tableId.'nav">
      <div>Page <span id="'.$this->tableId.'currentpage"></span> of <span id="'.$this->tableId.'totalpages"></span>
        <img src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/images/first.gif" width="16" height="16" alt="First Page" onclick="'.$this->tableId.'sorter.move(-1,true)" />
        <img src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/images/previous.gif" width="16" height="16" alt="First Page" onclick="'.$this->tableId.'sorter.move(-1)" />
        <img src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/images/next.gif" width="16" height="16" alt="First Page" onclick="'.$this->tableId.'sorter.move(1)" />
        <img src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/images/last.gif" width="16" height="16" alt="Last Page" onclick="'.$this->tableId.'sorter.move(1,true)" />
      </div>
      <div><select id="'.$this->tableId.'pagedropdown" style="display:none"></select></div>
      <!-- <div><a href="javascript:'.$this->tableId.'sorter.showall()">view all</a></div> -->
    </div>
    ';
  }

  function getTablenavArrowsOnly() {
    $frameworkUrl = getFrameworkUrl();
    return '<div class="tinytable-tablenav" id="'.$this->tableId.'nav">
      <div>
        <img src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/images/first.gif" width="16" height="16" alt="First Page" onclick="'.$this->tableId.'sorter.move(-1,true)" />
        <img src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/images/previous.gif" width="16" height="16" alt="First Page" onclick="'.$this->tableId.'sorter.move(-1)" />
        <img src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/images/next.gif" width="16" height="16" alt="First Page" onclick="'.$this->tableId.'sorter.move(1)" />
        <img src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/images/last.gif" width="16" height="16" alt="Last Page" onclick="'.$this->tableId.'sorter.move(1,true)" />
      </div>
      <div><select id="'.$this->tableId.'pagedropdown" style="display:none"></select></div>
      <!-- <div><a href="javascript:'.$this->tableId.'sorter.showall()">view all</a></div> -->
    </div>
    ';
  }

  /**
   * return the html representation of the table
   */
  function toHtml() {
    if ($this->invisibleIfEmpty && empty($this->rows)) {
      return;
    }

    $frameworkUrl = getFrameworkUrl();
    // only include the link to the css once! (check for tableid = table1)
    if ($this->sortableTable && $this->tableId == 'table1') {
      setHeadContent('<link rel="stylesheet" href="'.$frameworkUrl.'/thirdParty/tinyTable/tinyTableCustomStyle.css"/>');
    }

    if ($this->sortableTable) {
      $toReturn = '<div id="1" class="tinytable-tablewrapper">';

      $searchAddition = '';
      if (!$this->searchable) {
        $searchAddition = ' style="display:none"';
      }
      $toReturn .= '<div id="2" class="tinytable-tableheader"'.$searchAddition.'>';
      $toReturn .= '<div id="3" class="tinytable-search">
        <select style="display:none" id="'.$this->tableId.'columns" onchange="'.$this->tableId.'sorter.search(\''.$this->tableId.'query\')"></select>  
        <input type="text" placeholder="Filter by..." id="'.$this->tableId.'query" onkeyup="'.$this->tableId.'sorter.search(\''.$this->tableId.'query\')" />
      </div> <!-- 3 close -->';
      $toReturn .= '</div> <!-- 2 close -->';
      if (!empty($this->rows)) {
        $toReturn .= '<div id="4" class="tinytable-toptablenav">';
        $toReturn .= $this->getTablenav();
        $toReturn .= '<span class="tinytable-details"><div id="5">Records <span id="'.$this->tableId.'startrecord"></span>-<span id="'.$this->tableId.'endrecord"></span> of <span id="'.$this->tableId.'totalrecords"></span> </div> <!-- 5 close --></span>';
        $toReturn .= '</div> <!-- 4 close -->'; // -end "tableheader"
      }
    }
    // width="100%" on the table fixes the issue with the table shifting to the right when browser is wide
    if ($this->sortableTable) {
      $toReturn .= '<table width="100%" '.$this->tableAttributes.' id="'.$this->tableId.'" class="tinytable">';
    }
    else {
      $toReturn = '<table width="100%" '.$this->tableAttributes.'>';
    }

    if (isset($this->caption)) {
      $toReturn .= $this->caption;
    }

    if (isset($this->heading) && count($this->heading) > 0) {
      $toReturn .= "<thead>";
      if (isset($this->headingCSSClass)) {
        $toReturn .= '<tr class="'.$this->headingCSSClass.'">';
      }
      else {
        $toReturn .= '<tr>';
      }
      foreach ($this->heading as $element) {

        if ($this->sortableTable) {
          $toReturn .= '<th><h3>'.$element.'</h3></th>';
        }
        else {
          $toReturn .= '<th>';
          $toReturn .= $element;
          $toReturn .= '</th>';
        }
      }
      $toReturn .= '</tr>';
      $toReturn .= "</thead>";
    }

    $toReturn .= "<tbody>".$this->rows."</tbody>";
    $toReturn .= '</table>';

    if ($this->sortableTable) {
//      $toReturn .= '<div class="tinytable-tablefooter">';
//      if ($this->entriesPerPage > 0) {
//        $toReturn .= $this->getTablenavArrowsOnly();
//      }

//      $toReturn .= '</div>'; // -end "tablefooter"


      $toReturn .= '</div> <!-- 1 close -->'; // -end "tablewrapper"

      $initialPagingSize = $this->entriesPerPage;

      $pagingStuff = '';
      if ($this->entriesPerPage > 0) {
        $pagingStuff = 'paginate:true,size:'.$this->entriesPerPage.',';
      }
      else {
        $pagingStuff = 'paginate:true,size:10000000000000000,';
      }

      $toReturn .= '<script type="text/javascript" src="'.$frameworkUrl.'/thirdParty/tinyTable/TinyTableV3/script.js"></script> 
<script type="text/javascript">
var '.$this->tableId.'sorter = new TINY.table.sorter(\''.$this->tableId.'sorter\', \''.$this->tableId.'\', {
headclass:\'head\',
ascclass:\'asc\',
descclass:\'desc\',
evenclass:\'evenrow\',
oddclass:\'oddrow\',
evenselclass:\'evenselected\',
oddselclass:\'oddselected\','.$pagingStuff.'
colddid:\''.$this->tableId.'columns\',
currentid:\''.$this->tableId.'currentpage\',
totalid:\''.$this->tableId.'totalpages\',
startingrecid:\''.$this->tableId.'startrecord\',
endingrecid:\''.$this->tableId.'endrecord\',
totalrecid:\''.$this->tableId.'totalrecords\',
hoverid:\'tinytable-selectedrow\',
pageddid:\''.$this->tableId.'pagedropdown\',
navid:\''.$this->tableId.'nav\',
sortdir:1,
init:true  });
</script>';
    }

    return $toReturn;
  }

}

?>
