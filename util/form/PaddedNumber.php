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

class PLF_PaddedNumber extends PLF_Element {
  var $numDigits;

  function __construct($name, $label, $numDigits, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->numDigits = $numDigits;
  }

  function PLF_PaddedNumber($name, $label, $numDigits, $required) {
    self::__construct($name, $label, $numDigits, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = ' <input  '.$this->getDisabledAttribute().' type="text" tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'" size="'.$this->numDigits.'" maxlength="'.$this->numDigits.'"';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= ' value="'.htmlspecialchars($this->getValue()).'"';
    }
    $toReturn .= ' '.$this->attribute.' />';
    return $toReturn;
  }


  function validate() {
    // call standard parent validation method (required field)
    // then do own validation
    if (parent::validate()) {
      if (strlen($this->value ?? '') > 0) {
        if (!is_numeric($this->value)) {
          $this->requiredText = 'please enter a number';
          $isValid = false;
        }
        // todo ,weihongs check here.
        else if (!preg_match('/^\d{'.$this->numDigits.'}$/', $this->value)) {
          $this->requiredText = 'please enter exactly '.$this->numDigits.' digits';
          $isValid = false;
        }
        else {
          return true;
        }
      }
      else {
        return true;
      }
    }
    else {
      return false;
    }
  }

}

?>
