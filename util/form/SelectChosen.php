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

class PLF_ChosenSelect extends PLF_Element {
  var $values;
  var $onChange;

  function __construct($name, $label, $values, $required, $onChange) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->onChange = $onChange;
    //  $this->values = $values;
    //  $this->values[''] = ''; // always will want an empty element in a select dropdown

    // changed this so that the empty element we add will be at the top of the list
    // not the bottom
    $empty = array('' => '');
    $this->values = $empty + $values;
  }

  function PLF_ChosenSelect($name, $label, $values, $required, $onChange) {
    self::__construct($name, $label, $values, $required, $onChange);
  }


  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $onChangeText = '';
    if (isset($this->onChange)) {
      $onChangeText = 'onChange="'.$this->onChange.'"';
    }
    $toReturn = ' <select  '.$this->getDisabledAttribute().' '.$onChangeText.' tabindex="'.$tabIndex.'" name="'.$this->getName().'" class="chosen-select" id="'.$this->getName().'" '.$this->attribute.' >';

    foreach ($this->values as $key => $value) {
      $toReturn .= '<option value="'.$key.'"';
      if ($this->value == $key) {
        $toReturn .= ' selected ="selected"';
      }
      $toReturn .= '>'.htmlspecialchars($value);
      $toReturn .= '</option>';
    }


    $toReturn .= '</select>';
    $toReturn .= '<script type="text/javascript">
 $(".chosen-select").chosen({allow_single_deselect: true, search_contains:true});
 </script>';
    return $toReturn;
  }

  function validate() {
    // call standard parent validation method (required field)
    // then do own validation
    if (parent::validate()) {
      if (strlen($this->value ?? '') > 0) {
        /*
         * This validation below protects against a bad boy (or girl)
         * modifying the webform (or typing directly in the url window)
         * to pass a value that is not in the list of values to choose
         * from
         */
        if (!array_key_exists($this->value, $this->values)) {
          $this->requiredText = 'Please select a valid value from the list';
          return false;
        }
      }
    }
    else {
      return false;
    }
    return true;
  }


}

?>
