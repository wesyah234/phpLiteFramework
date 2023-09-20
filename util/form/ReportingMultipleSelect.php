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


class PLF_ReportingMultipleSelect extends PLF_MultipleSelectionElement {
  var $size;

  function __construct($name, $label, $values, $size, $required) {
    PLF_MultipleSelectionElement::PLF_MultipleSelectionElement($name, $label, $values, $required);
    $this->size = $size;
  }

  function PLF_ReportingMultipleSelect($name, $label, $values, $size, $required) {
    self::__construct($name, $label, $values, $size, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = ' <select  '.$this->getDisabledAttribute().' multiple id="'.$this->getName().'" size="'.$this->size.'" tabindex="'.$tabIndex.'" name="'.$this->getName().'[]" onChange="selectMultipleChanged(\''.$this->getName().'\');" '.$this->attribute.' >';

    if (isset($this->values)) {
      foreach ($this->values as $key => $value) {
        $toReturn .= '<option value="'.$key.'"';
        // see if the $key we're currently looking at is in the array
        // of values posted, if it is, mark this option value as selected
        if (isset($this->value) && in_array($key, $this->value)) {
          $toReturn .= ' selected ="selected"';
        }
        $toReturn .= '>'.htmlspecialchars($value);
        $toReturn .= '</option>';
      }

      $toReturn .= '</select>';
      $toReturn .= '<script type="text/javascript">';
      $toReturn .= 'selectMultipleChanged(\''.$this->getName().'\');';
      $toReturn .= '</script>';
    }

    return $toReturn;
  }


  /**
   * override default because we want to have the div in here for the javascript
   * to place the currently selected values
   */
  function getLabel() {
    return $this->label.' <span id="'.$this->getName().'-div"></span>';
  }

}

?>
