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

class PLF_ReadOnlyDate extends PLF_Element {
  var $size;

  function __construct($name, $label, $size) {
    PLF_Element::PLF_Element($name, $label, false);
    $this->size = $size;
  }

  function PLF_ReadOnlyDate($name, $label, $size) {
    self::__construct($name, $label, $size);
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

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $theValue = $this->getValue();
    $theSize = $this->size;
    $toReturn = "<u>";
    if (empty($theValue)) {
      for ($i = 0; $i < $theSize; $i++) {
        $toReturn .= '&nbsp;';
      }
    }
    else {
      $toReturn .= htmlspecialchars($theValue);
    }
    $toReturn .= '</u>';
    $toReturn .= ' <input type="hidden" tabindex="'.$tabIndex.'" id="'.$this->getName().'" name="'.$this->getName().'"';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= ' value="'.htmlspecialchars($this->getValue()).'"';
    }
    $toReturn .= ' />';
    return $toReturn;
  }

  function validate() {
    return true;
  }
}

?>
