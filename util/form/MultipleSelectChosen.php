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


class PLF_MultipleSelectChosen extends PLF_MultipleSelectionElement {

  function __construct($name, $label, $values, $required) {
    PLF_MultipleSelectionElement::PLF_MultipleSelectionElement($name, $label, $values, $required);
  }

  function PLF_MultipleSelectChosen($name, $label, $values, $required) {
    self::__construct($name, $label, $values, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $name = $this->getName();
    $toReturn = ' <select  '.$this->getDisabledAttribute().' multiple id="'.$this->getName().'" tabindex="'.$tabIndex.'" name="'.$this->getName().'[]" '.$this->attribute.' class="chosen-select" >';

    if (isset($this->values)) {
      foreach ($this->values as $key => $value) {
        $concatenated = $name.'--'.$key;
        $toReturn .= '<option id="'.$concatenated.'"  value="'.$key.'"';
        // see if the $key we're currently looking at is in the array
        // of values posted, if it is, mark this option value as selected
        if (isset($this->value) && in_array($key, $this->value)) {
          $toReturn .= ' selected ="selected"';
        }
        $toReturn .= '>'.htmlspecialchars($value);
        $toReturn .= '</option>';
      }

      $toReturn .= '</select>';
      $toReturn .= '<script type="text/javascript">
 $(".chosen-select").chosen({search_contains:true, width:"100%"});
 </script>';
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
