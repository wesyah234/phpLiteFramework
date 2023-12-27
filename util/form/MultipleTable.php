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
require_once 'MultipleSelectionElement.php';


class PLF_MultipleTable extends PLF_MultipleSelectionElement {
// first element will be the key for the checkbox.


  var $header;

  function __construct($name, $label, $header, $values, $numRows, $required) {
    PLF_MultipleSelectionElement::PLF_MultipleSelectionElement($name, $label, $values,  $required);
    $this->header = $header;
    $this->numRows = $numRows;
  }

  function PLF_MultipleTable($name, $label, $header, $values, $numRows, $required) {
    self::__construct($name, $label, $header, $values, $numRows, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = '<span id="'.$this->name.'">';

    $headerRow = array();
    array_push($headerRow, '');
    $headerRow = array_merge($headerRow, $this->header);
    $table = newTableOrig($headerRow);
    // currently the NEW table datatables is not working here, it only is seeing checked boxes for the currently visible page in the table... so swap back the original one for now
    // will post question to datatables forum when I have a test prog ready
    //$table = newTable($headerRow);
    $table->setFancy(true, $this->numRows);
    foreach ($this->values as $value) {
      //$key = current($value);
      // note: we're popping off the key now so if you want to display the key in the table, just select it twice.
      $key = array_shift($value);
      $labelFor = $this->getName().'--'.$key;

      $checkbox = '<input  '.$this->getDisabledAttribute().' type="checkbox" id="'.$labelFor.'" tabindex="'.$tabIndex.'" name="'.$this->getName().'[]" id="'.$this->getName().'" value="'.$key.'"';
      // see if the $key we're currently looking at is in the array
      // of values posted, if it is, mark this option value as selected
      if (isset($this->value) && in_array($key, $this->value)) {
        $checkbox .= ' checked ="checked"';
      }
      //      $toReturn .= '>'.htmlspecialchars($value);
      $checkbox .= ' '.$this->attribute.' ><label for="'.$labelFor.'">';
      $checkbox .= '</label></input>';

      $row = array();
      array_push($row, $checkbox);
      $row = array_merge($row, $value);
      $table->addRow($row);
    }
    $toReturn .= $table->toHtml();
    $toReturn .= "</span>";
    return $toReturn;
  }

/** we must override the parent validation method since the values are not stored like all the other multiple selection controls*/
  function validate() {
    $isValid = True;
    if ($this->required) {
      // do count and isset here since it's an array instead
      // of inheriting this method which is just string based
      if (!isset($this->value)) {
        $this->requiredText = 'this field is required';
        $isValid = false;
      }
      else if (count($this->value) == 0) {
        $this->requiredText = 'this field is required';
        $isValid = false;
      }
      else {
        $isValid = true;
      }
    }
    else {
      $isValid = true;
    }
    if ($isValid) {
      if (isset($this->value) && count($this->value) > 0) {
        // pull out the first element
        $validValues = array();
        foreach ($this->values as $row) {
          $validValues[] = current($row);
        }
        foreach ($this->value as $userValue) {
          if (!in_array($userValue, $validValues)) {
            $this->requiredText = 'Please select only valid values from the table';
            $isValid = false;
            break;
          }
        }
      }
    }
    return $isValid;
  }

}

?>
