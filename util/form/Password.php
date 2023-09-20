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

class PLF_Password extends PLF_Element {
  var $maxlength;
  var $size;

  function __construct($name, $label, $size, $maxlength, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->maxlength = $maxlength;
    $this->size = $size;
  }

  function PLF_Password($name, $label, $size, $maxlength, $required) {
    self::__construct($name, $label, $size, $maxlength, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = ' <input  '.$this->getDisabledAttribute().' type="password" tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'" size="'.$this->size.'" maxlength="'.$this->maxlength.'"';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= ' value="'.htmlspecialchars($this->getValue()).'"';
    }
    $toReturn .= ' '.$this->attribute.' />';
    return $toReturn;
  }

  function validate() {
    // call standard parent validation method (required field)
    // then call own validation on the length
    if (parent::validate($this->value)) {
      // manually check the string length in case the user is
      // a bad boy (or girl) and modifies a local copy of the 
      // web form and changes the maxlength attribute on the form
      // element
      if (strlen($this->value ?? '') > $this->maxlength) {
        $this->requiredText = 'please enter less than '.$this->maxlength.' characters';
        $isValid = false;
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
