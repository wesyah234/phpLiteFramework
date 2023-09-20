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

class PLF_MultipleCheckbox extends PLF_MultipleSelectionElement {
  var $delimiter;

  function __construct($name, $label, $values, $required, $delimiter) {
    PLF_MultipleSelectionElement::PLF_MultipleSelectionElement($name, $label, $values, $required);
    $this->delimiter = $delimiter;
  }

  function PLF_MultipleCheckbox($name, $label, $values, $required, $delimiter) {
    self::__construct($name, $label, $values, $required, $delimiter);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = '<span id="'.$this->name.'">';
    if (isset($this->values)) {
      foreach ($this->values as $key => $value) {
        $labelFor = $this->getName().'--'.$key;
        $toReturn .= '<input  '.$this->getDisabledAttribute().' type="checkbox" id="'.$labelFor.'" tabindex="'.$tabIndex.'" name="'.$this->getName().'[]" id="'.$this->getName().'" value="'.$key.'"';
        // see if the $key we're currently looking at is in the array
        // of values posted, if it is, mark this option value as selected
        if (isset($this->value) && in_array($key, $this->value)) {
          $toReturn .= ' checked ="checked"';
        }
        //      $toReturn .= '>'.htmlspecialchars($value);
        $toReturn .= ' '.$this->attribute.' ><label for="'.$labelFor.'">'.$value;
        $toReturn .= '</label></input>'.$this->delimiter;
      }
    }
    $toReturn .= "</span>";
    return $toReturn;
  }
}

?>
