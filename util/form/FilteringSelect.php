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

/**
 * EXPERIMENTAL...
 */
class PLF_FilteringSelect extends PLF_Element {
  var $onChange;
  var $url;

  function __construct($name, $label, $url, $required, $onChange) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->onChange = $onChange;
    $this->url = $url;
  }

  function PLF_FilteringSelect($name, $label, $url, $required, $onChange) {
    self::__construct($name, $label, $url, $required, $onChange);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $frameworkUrl = getFrameworkUrl();
    $toReturn = '';
    setHeadContent('<link rel="STYLESHEET" type="text/css" href="'.$frameworkUrl.'/thirdParty/dhtmlx/dhtmlxCombo/codebase/dhtmlxcombo.css"><script  src="'.$frameworkUrl.'/thirdParty/dhtmlx/dhtmlxCombo/codebase/dhtmlxcommon.js"></script><script src="'.$frameworkUrl.'/thirdParty/dhtmlx/dhtmlxCombo/codebase/dhtmlxcombo.js"></script><script>window.dhx_globalImgPath = "'.$frameworkUrl.'/thirdParty/dhtmlx/dhtmlxCombo/codebase/imgs/";</script>');
    $onChangeText = '';
    if (isset($this->onChange)) {
      $onChangeText = 'onChange="'.$this->onChange.'"';
    }
    $toReturn .= ' <select  '.$this->getDisabledAttribute().' '.$onChangeText.' style=\'width:200px;\' tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'" '.$this->attribute.' >';

    $toReturn .= '</select>';
    $toReturn .= '<script> var z = new dhtmlXComboFromSelect("'.$this->getName().'");
z.enableFilteringMode(true, "'.$this->url.'", true);</script>';
    return $toReturn;
  }

  function validate() {
    // call standard parent validation method (required field)
    // then do own validation
    if (parent::validate()) {
    }
    else {
      return false;
    }
    return true;
  }


}

?>
