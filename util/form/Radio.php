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
require_once 'Element.php';

class PLF_Radio extends PLF_Element {
  var $values;
  var $delimiter;

  function __construct($name, $label, $values, $required, $delimiter) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->values = $values;
    $this->delimiter = $delimiter;

  }

  function PLF_Radio($name, $label, $values, $required, $delimiter) {
    self::__construct($name, $label, $values, $required, $delimiter);

  }

  // todo : maybe pass in some "enclosing" characters so that we can tell it to enclose each radio button in the following:
  // <td style='text-align:center'>
  // and
  // </td>
  // or fake it out by using: 
  // </td><td style='text-align:center'>
  // as the delimiter!

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = '<span id="'.$this->name.'">';
    $name = $this->getName();
    foreach ($this->values as $key => $value) {
      // charisma framework was adding a display:block here, so we hard code a display:inherit so that 
      // we can still achieve radio buttons on a single line by using the $delimiter argument 
      $concatenated = $name.'--'.$key;
      $toReturn .= '<label style="display:inherit;"><input id="'.$concatenated.'" '.$this->getDisabledAttribute().' type="radio" tabindex="'.$tabIndex.'" name="'.$name.'" value="'.$key.'"';
      if (isReallySet($this->value) && $key == $this->value) {
        $toReturn .= ' checked="checked"';
      }
      $toReturn .= ' '.$this->attribute.' >'.$value;
      $toReturn .= '</input>'.$this->delimiter;
      $toReturn .= "</label>";
    }
    $toReturn .= "</span>";
    return $toReturn;
  }

  function renderSingleRadioField($key) {
    $toReturn = '';
    $value = $this->values[$key];
    $toReturn .= '<label style="display:inherit;"><input '.$this->getDisabledAttribute().' type="radio" name="'.$this->getName().'" value="'.$key.'"';
    if (isReallySet($this->value) && $key == $this->value) {
      $toReturn .= ' checked="checked"';
    }
    $toReturn .= ' '.$this->attribute.' >'.$value;
    $toReturn .= '</input>'.$this->delimiter;
    $toReturn .= "</label>";
    return $toReturn;
  }

  function validate() {
    // call standard parent validation method (required field)
    // then do own validation
    if (parent::validate()) {
      if (strlen($this->value ?? '') > 0) {
        /*
         * This validation below protects against a bad boy (or girl)
         * modifying the webform (or typing directly in the url window)
         * to pass a value that is not in the list of values to choose
         * from
         */
        if (!array_key_exists($this->value, $this->values)) {
          $this->requiredText = 'Please select a valid value from the list';
          return false;
        }
      }
    }
    else {
      return false;
    }
    return true;
  }


}

?>
