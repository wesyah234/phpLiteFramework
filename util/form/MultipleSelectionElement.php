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

class PLF_MultipleSelectionElement extends PLF_Element {
  var $values;

  function __construct($name, $label, $values, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->values = $values;
  }

  function PLF_MultipleSelectionElement($name, $label, $values, $required) {
    self::__construct($name, $label, $values, $required);
  }

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
        foreach ($this->value as $userValue) {
          if (!array_key_exists($userValue, $this->values)) {
            $this->requiredText = 'Please select only valid values from the list';
            $isValid = false;
            break;
          }
        }
      }
    }
    return $isValid;
  }

// override default setValue method since these are being stored in db like this:
// |45|3|4|
// and we want to store an actual array of these values internally in the object
// the conversion back is done in the getValueForDb method below
// the inherited getValue() method just returns the internal array object, not the delimited str

// technically we could have a setValue and a setValueFromDb methods, but we have the
// ability to interrogatge the type of the incoming argument and deal with it internally

  function setValue($inValue) {
    if (NULL == $inValue) {
      $this->value = NULL;
    }
    else if (is_array($inValue)) {
      $this->value = $inValue;
    }
    else {
      $newThisValue = explode('|', substr($inValue ?? '', 1, -1));
      $this->value = $newThisValue;
    }
  }

  function getValueForDb() {
    if (NULL == $this->value) {
      // doing this eliminates storing || in the db, instead we
      // just store NULL.
      return NULL;
    }
    else {
      return '|'.implode('|', $this->value).'|';
    }
  }

}

?>
