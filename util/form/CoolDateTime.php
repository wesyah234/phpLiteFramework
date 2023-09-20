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

class PLF_CoolDateTime extends PLF_Element {

  function __construct($name, $label, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
  }

  function PLF_CoolDateTime($name, $label, $required) {
    self::__construct($name, $label, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = '<input  '.$this->getDisabledAttribute().' size=20 '.$this->attribute.' type="text" tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'" size="10"';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= ' value="'.$this->getValue().'"';
    }
    $toReturn .= ' />';

    $toReturn .= '&nbsp;<img src="'.getFrameworkUrl().'/thirdParty/dhtmlCalendar/jscalendar-1.0/img.gif'.'" id="';
    $toReturn .= $this->name.'trigger';
    $toReturn .= '" '.$this->attribute.' ';
    // don't do the onclick javascript if this form element is disabled
    if (!$this->getDisabled()) {
      $toReturn .= ' onclick="javascript:NewCssCal(\''.$this->getName().'\', \'MMddyyyy\', \'arrow\', \'true\', \'12\')" style="cursor:pointer"  ';
    }
    $toReturn .= ' />';

    return $toReturn;
  }

  function dateValid($date) {
    $isValid = false;
    $dateArr = preg_split('/[,\/-]/', $date);
    if (count($dateArr) == 3) {
      $m = (int)$dateArr[0];
      $d = (int)$dateArr[1];
      $y = (int)$dateArr[2];
      $isValid = checkdate($m, $d, $y);
    }
    if (!$isValid || $y < 1000) {
      $isValid = false; // set it to false in case of the $y < 1000 situation
    }
    return $isValid;
  }

  function timeValid($time) {
    $isValid = true;
    $matches = array();
    if (preg_match("/(0?\d|1[0-2]):(0\d|[0-5]\d) (AM|PM)/i", $time, $matches) === 0) {
      $isValid = false;
    }
    return $isValid;

  }

  function validate() {
    // call standard parent validation method (required field)
    // then do own validation
    if (parent::validate()) {
      $isValid = true;
      if (strlen($this->value ?? '') > 0) {
        $isValid = false;
        $firstSpace = strpos($this->value, " ");
        if ($firstSpace === false) {
          $this->requiredText = 'please enter a valid date time (mm/dd/yyyy HH:MM AM)';
          $isValid = false;
        }
        else {
          $datePart = substr($this->value ?? '', 0, $firstSpace);
          $timePart = substr($this->value ?? '', $firstSpace + 1);
          if ($this->dateValid($datePart) && $this->timeValid($timePart)) {
            $isValid = true;
          }
          else {
            $this->requiredText = 'please enter a valid date time (mm/dd/yyyy HH:MM AM)';
            $isValid = false;
          }
        }
        // sometimes, a value like 1. can be converted to a 1 and validated as a valid date
        // however, the database wouldn't like such a date "5/1./2013" for example
        // so, take the $m $d $y pieces and rebuild the date/time with slashes
        // this is also done in CoolDate      
        if ($isValid) {
          $dateArr = preg_split('/[,\/-]/', $datePart);
          $m = (int)$dateArr[0];
          $d = (int)$dateArr[1];
          $y = (int)$dateArr[2];
          $this->value = "$m/$d/$y $timePart";
        }
      }
      return $isValid;
    }
    else {
      return false;
    }
  }

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
