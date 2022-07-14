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

class PLF_ReadOnlyLabel extends PLF_Element {

  function __construct($name, $label) {
    PLF_Element::PLF_Element($name, $label, false);
  }

  function PLF_ReadOnlyLabel($name, $label) {
    self::__construct($name, $label);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
//    return $this->label;
// a ReadOnlyLabel should only be used as a label, the render
// method here shouldn't return anything because it isn't really
// a form element
    return '';
  }

  function validate() {
    return true;
  }
}

?>
