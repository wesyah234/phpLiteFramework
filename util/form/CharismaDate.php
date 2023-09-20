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
require_once 'CoolDate.php';

/** This is equivalent to the normal PLF_Date, however it sets class="datepicker"
 * which enables it to do a javascripty date popup, which works much better
 * when using the Charisma Framework than the original CoolDate
 * This requires one to be using the Charisma Framework in their application:
 * http://usman.it/free-responsive-admin-template/
 * Based on Bootstrap:
 * http://twitter.github.com/bootstrap/index.html
 *
 */
class PLF_CharismaDate extends PLF_CoolDate {

  function __construct($name, $label, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
  }

  function PLF_CharismaDate($name, $label, $required) {
    self::__construct($name, $label, $required);
  }

  // MyForm class will set tabindex before calling this method
  // override render:
  function render($tabIndex) {
    $toReturn = '<input '.$this->getDisabledAttribute().' '.$this->attribute.' class = "datepicker" type="text" tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'" size="10" ';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= ' value="'.$this->getValue().'"';
    }
    $toReturn .= ' />';
    return $toReturn;
  }

// pick up validate from parent

  /**
   * this previous version didn't work since strtotime only works on
   * years > 1970
   *
   * function validateOld() {
   * $isValid = True;
   * if (strlen($this->value) > 0) {
   * $timestamp = strtotime($this->value);
   * if (-1 == $timestamp) {
   * $this->requiredText = 'please enter a valid date';
   * $isValid = false;
   * }
   * else {
   * // format correctly for our internal representation
   * // so db is happy
   * $this->value = date(PHPDATEFORMAT, $timestamp);
   * }
   * }
   * else if ($this->required) {
   * $this->requiredText = 'this field is required';
   * $isValid = false;
   * }
   * return $isValid;
   * }
   */
}

?>
