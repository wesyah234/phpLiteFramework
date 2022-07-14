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

class PLF_Checkbox extends PLF_Element {

  function __construct($name, $label) {
    // required doesn't apply to checkboxes
    PLF_Element::PLF_Element($name, $label, false);
  }

  function PLF_Checkbox($name, $label) {
    self::__construct($name, $label);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = ' <input '.$this->getDisabledAttribute().' type="checkbox" tabindex="'.$tabIndex.'" name="'.$this->name.'" id="'.$this->name.'"';
    if ($this->value) {
      $toReturn .= ' checked ="checked"';
    }
    $toReturn .= ' '.$this->attribute.' />';
    return $toReturn;
  }

  function setValue($value) {
    if (!isReallySet($value)) {
      $this->value = false;
    }
    else if ('on' === $value) {
      $this->value = true;
    }
    else if ('t' === $value) {
      $this->value = true;
    }
    else if ('1' === $value) {
      $this->value = true;
    }
    else if (CHECKBOX_CHECKED === $value) {
      $this->value = true;
    }
    else if (CHECKBOX_CHECKED == $value) {
      $this->value = true;
    }
    else {
      $this->value = false;
    }
  }

  function getValueForDb() {
    if ($this->value) {
      return CHECKBOX_CHECKED;
    }
    else {
      return CHECKBOX_UNCHECKED;
    }
  }

  function validate() {
    return true;
  }
}

?>
