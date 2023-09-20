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

class PLF_Element {
  var $name;
  var $label;
  var $required;

  var $requiredText;
  var $value;

  var $attribute;

  var $hide = false;

  var $disabled = false;


  function __construct($name, $label, $required) {
    $this->name = $name;
    $this->label = $label;
    $this->required = $required;
  }

  function PLF_Element($name, $label, $required) {
    self::__construct($name, $label, $required);
  }

  function setDisabled($disabled) {
    $this->disabled = $disabled;
  }

  function getDisabled() {
    return $this->disabled;
  }

  function getDisabledAttribute() {
    return $this->disabled ? 'disabled' : '';
  }

  function hide() {
    $this->hide = true;
  }

  function show() {
    $this->hide = false;
  }

  function setAttribute($attribute) {
    $this->attribute = $attribute;
  }

  function getName() {
    return $this->name;
  }


  /**
   * subclasses will override if they want something different than just the label
   * text.  ex: Textarea will override and also print out the max number of characters
   * allowed in the textarea field.
   */
  function getLabel() {
    return $this->label;
  }

  function setLabel($label) {
    $this->label = $label;
  }

  function setRequiredText($requiredText) {
    $this->requiredText = $requiredText;
  }

  function getRequiredText() {
    return $this->requiredText;
  }

  /**
   * Sets the internal value.  Here we will ignore any array, since the subclass
   * MultipleSelectionElement(and its subclasses) override this method to specifically
   * deal with arrays.
   *
   * This filtering of arrays protects against unscrupulous users changing
   * query/post parameters from say, FIRSTNAME, to FIRSTNAME[], in an a attempt to generate
   * php errors and figure out the inner workings of the code...
   *
   * Read this in PHP|Architect Vol5 Iss2:
   * "Doing it Japanese style" by Marco Tabini
   *
   * other functions in the framework that assist in this effort outside of the MyForm class
   * are: getRequestVarNum(), getRequestVarString(), and getRequestVarArray().  These force
   * the developer to specifically pronounce what datatype they expect up front, reducing the
   * problem introduced when a user adds [] to a parameter expected to not be an array, or removes
   * [] from a parameter expected to be an array.
   *
   */
  function setValue($value) {
    if (!is_array($value)) {
      $this->value = $value;
    }
  }

  function setRequired($value) {
    $this->required = $value;
  }

  function getValue() {
    return $this->value;
  }

  /**
   * override in subclass if you don't want the internal $this->value
   * returned for the db insert (like for the MultipleSelectionElement,
   * where we internally store an array, but on the db insert, we want a
   * delimited string built from the array, for exampls: |3|88|33| )
   */
  function getValueForDb() {
    return $this->value;
  }


  function renderLabel() {
    $toReturn = $this->getLabel();
    if ($this->required) {
      $toReturn .= '<font color="red"> * </font>';
    }
    if (isReallySet($this->requiredText)) {
      $toReturn .= ' <font color="red">'.$this->requiredText.'</font>';
    }
    return $toReturn;
  }

  function validate() {
    $isValid = True;
    if ($this->required) {
      if (strlen($this->value ?? '') > 0) {
        $isValid = true;
      }
      else {
        $this->requiredText = 'required';
        $isValid = false;
      }
    }
    else {
      $isValid = true;
    }
    return $isValid;
  }
}

?>
