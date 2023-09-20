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

class PLF_WholeNumber extends PLF_Element {
  var $minValue;
  var $maxValue;

  function __construct($name, $label, $minValue, $maxValue, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
    // we put this restriction here because when we attempt to validate that user input is within
    // the min/max value, we must cast to int, and if we're working with numbers bigger than
    // the PHP_INT_MAX (on either side of zero) we will have unpredictable results.
    // 
    // If you need to take in and validate user input up to a range past the max, consider 
    // using a Text field in your form, and doing your own validation. (possibly using the 
    // bcmath functions: http://us2.php.net/bc)
    if ($maxValue > PHP_INT_MAX || $minValue < -PHP_INT_MAX) {
      logError("Error on field $name, the range of values for whole number validation must be between -".PHP_INT_MAX.' and '.PHP_INT_MAX.', inclusive.');
    }

    $this->minValue = $minValue;
    $this->maxValue = $maxValue;
  }

  function PLF_WholeNumber($name, $label, $minValue, $maxValue, $required) {
    self::__construct($name, $label, $minValue, $maxValue, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $length = strlen(max($this->maxValue, abs($this->minValue)) ?? '') + 2;
    $maxLength = $length + 20;
    $toReturn = ' <input  '.$this->getDisabledAttribute().' type="text" tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'" size="'.$length.'" maxlength="'.$maxLength.'"';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= ' value="'.htmlspecialchars($this->getValue()).'"';
    }
    $toReturn .= ' '.$this->attribute.' />';
    return $toReturn;
  }


  function validate() {
    // call standard parent validation method (required field)
    // then do own validation
    $this->value = trim($this->value);
    if (parent::validate()) {
      if (strlen($this->value ?? '') > 0) {
        if (!is_numeric($this->value)) {
          $this->requiredText = 'please enter a number';
          $isValid = false;
        }
        // here (and below) we have to add 0 to the number before casting it so that it will
        // properly deal with numbers provided to us in scientific nocation as a string
        else if ($this->value > $this->maxValue || $this->value < $this->minValue || (int)($this->value + 0) != $this->value) {
          $this->requiredText = 'please enter a whole number between '.$this->minValue.' and '.$this->maxValue;
          $isValid = false;
        }
        else {
          // here we also have to add 0 before the cast so that it handles scientific
          // notation properly
          $this->value = (int)($this->value + 0);
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
