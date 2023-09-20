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

class PLF_Textarea extends PLF_Element {
  var $rows;
  var $cols;
  var $maxChars;

  function __construct($name, $label, $rows, $cols, $maxChars, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->rows = $rows;
    $this->cols = $cols;
    $this->maxChars = $maxChars;
  }

  function PLF_Textarea($name, $label, $rows, $cols, $maxChars, $required) {
    self::__construct($name, $label, $rows, $cols, $maxChars, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
//    $toReturn = ' <textarea tabindex="'.$tabIndex.'" name="'.$this->getName().'" rows="'.$this->rows.'" cols="'.$this->cols.'" onKeyDown="textCounter(this.form.'.$this->getName().',this.form.remLen'.$this->getName().','.$this->maxChars.');" onKeyUp="textCounter(this.form.'.$this->getName().',this.form.remLen'.$this->getName().','.$this->maxChars.');">';
    $toReturn = ' <textarea  '.$this->getDisabledAttribute().' tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'" rows="'.$this->rows.'" cols="'.$this->cols.'" '.$this->attribute.' >';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= $this->getValue(); // don't mess with data at all
    }
    $toReturn .= '</textarea>';
//    $toReturn .= '<input readonly type="text" name="remLen'.$this->getName().'" size="3" value="'.($this->maxChars - strlen($this->getValue())).'">';
    return $toReturn;
  }


  function validate() {
    // call standard parent validation method (required field)
    // then call own validation on the length
    if (parent::validate($this->value)) {
      // manually check the string length 
      // todo (add the javascript length checker back in..
      // this was in the PLF at one time, but removed 
      // because it didn't work with some browsers)
      // Even if the JS length checker is put back in, 
      // this length check will still need to be here in case
      // browser has JS disabled
      if (strlen($this->value ?? '') > $this->maxChars) {
        $this->requiredText = 'please enter less than '.$this->maxChars.' characters';
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
