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

class PLF_CoolDate extends PLF_Element {

  function __construct($name, $label, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
  }

  function PLF_CoolDate($name, $label, $required) {
    self::__construct($name, $label, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    // id must contain the form name to be unique if there are multiple forms on a single page
    $toReturn = '<input  '.$this->getDisabledAttribute().' '.$this->attribute.' type="text" tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getId().'" size="10"';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= ' value="'.$this->getValue().'"';
    }
    $toReturn .= ' />';

    $toReturn .= '&nbsp;<img src="'.getFrameworkUrl().'/thirdParty/dhtmlCalendar/jscalendar-1.0/img.gif'.'" id="';
    // don't display the id of the trigger image if this form element is disabled
    // (this will cause the javascript to not be linked ot the calendar image... this is what we want: if disabled
    // we don't want the user to be clicking to bring up a calendar control)
    if (!$this->getDisabled()) {
      //$toReturn .= $this->name.'trigger';
      $toReturn .= $this->getParentForm()->formName. '-'. $this->name.'trigger';
    }
    $toReturn .= '" '.$this->attribute.' />';

    return $toReturn;
  }

  function validate() {
    // call standard parent validation method (required field)
    // then do own validation
    if (parent::validate()) {
      $isValid = true;
      if (strlen($this->value ?? '') > 0) {
        $isValid = false;
//        $dateArr = split('[-,/]', $this->value);
// split deprecated in php 5.3, replace with preg_split:
//        $dateArr = preg_split('/\-|\//', $this->value);
// from glenn, slight modification...
        $dateArr = preg_split('/[,\/-]/', $this->value);
        if (count($dateArr) == 3) {
          $m = (int)$dateArr[0];
          $d = (int)$dateArr[1];
          $y = (int)$dateArr[2];
          $isValid = checkdate($m, $d, $y);
        }
        // check for < 1000 to account for people who are using a 2 digit year
        // and check for > 9999 since checkdate will go up to 32767, but databases
        // don't go that high for a year.
        if (!$isValid || $y < 1000 || $y > 9999) {
          $isValid = false; // set it to false in case of the $y < 1000 situation
          $this->requiredText = 'please enter a valid date';
        }
        // then, check for month or day greater than 2 characters,
        // and also a year greater than 4 characters..
        // these aren't caught above because we convert the parts to a numeric INT, but
        // when we send the date to the db, we use the original string the user typed, and
        // some dbs don't like a leading zero in a month, day, or year component.
        if ($isValid && (strlen($dateArr[0] ?? '') > 2 || strlen($dateArr[1] ?? '') > 2 || strlen($dateArr[2] ?? '') > 4)) {
          $isValid = false;
          $this->requiredText = 'please enter a valid date';
        }
        // sometimes, a value like 1. can be converted to a 1 and validated as a valid date
        // however, the database wouldn't like such a date "5/1./2013" for example
        // so, take the $m $d $y created above and rebuild the date with slashes
        if ($isValid) {
          $this->value = "$m/$d/$y";
        }
        else {
          $this->requiredText = 'please enter a valid date';
        }
      }
      return $isValid;
    }
    else {
      return false;
    }
  }

  function setValue($value) {
    if (isReallySet($value)) {
      // when this is called with a date that can be parsed as a DateTime, go ahead and parse it
      // and then reformat the data using the PHPDATEFORMAT so that the date popups will be happy
      try {
        $standardizedDate = new DateTime($value);
        // then, set the internal value using PHPDATEFORMAT
        parent::setValue($standardizedDate->format(PHPDATEFORMAT));
      }
        // however, if there is an exception parsing the date, proceed with setting the value anyway
        // so that the validate method will handle the user validation and give the user a chance
        // to correct the date they are typing in
      catch (Exception $e) {
        parent::setValue($value);
      }
    }
  }


  /**
   * override in subclass if you don't want the internal $this->value
   * returned for the db insert (like for the MultipleSelectionElement,
   * where we internally store an array, but on the db insert, we want a
   * delimited string built from the array, for exampls: |3|88|33| )
   */
  function getValueForDb() {
    // mysql likes dates to be in format YYYY-MM-DD
    // oracle (the way we set up the connection in the PLF) likes dates to be in the
    // format MM/DD/YYYY
    // so, use the constant ADODBDRIVER to see which driver we're using
    // and format the date appropriately.

    // internally, it's being stored as MM/DD/YYYY since the date pickers all like that format
    // so nothing to convert if oracle.

    if (strpos(ADODBDRIVER, 'mysql') !== false) {
      if (isReallySet($this->value)) {
        $standardizedDate = new DateTime($this->value);
        return $standardizedDate->format('Y-m-d');
      }
      else {
        return '';
      }
    }
    else {
      // just send the MM/DD/YYYY internal format
      // if we need to change the format for other dbs, add another elseif part above
      // and format as needed
      return $this->value;
    }
  }
}

?>
