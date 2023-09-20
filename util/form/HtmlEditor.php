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
 * NOTE: this has been removed from plf.  When I have time/need to put in a html editor, evaluate
 * best out there and integrate again.  Noticed wordpress 2.0 is using one (tinymce) that allows user to drag
 * the editor window down to make it longer to see more text..   Maybe look into this one
 */
class PLF_HtmlEditor extends PLF_Element {
  var $rows;
  var $cols;
  var $maxChars;

  function __construct($name, $label, $rows, $cols, $maxChars, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->rows = $rows;
    $this->cols = $cols;
    $this->maxChars = $maxChars;
  }

  function PLF_HtmlEditor($name, $label, $rows, $cols, $maxChars, $required) {
    self::__construct($name, $label, $rows, $cols, $maxChars, $required);
  }

  function render($tabIndex) {
    //return "<script type=\"text/javascript\"> var oFCKeditor = new FCKeditor( '".$this->name."' ) ;oFCKeditor.BasePath = '".$GLOBALS['FRAMEWORKDIR'].'/thirdParty/htmlEditor/fckeditor/'."';oFCKeditor.Height = ".$this->heightInPixels.";oFCKeditor.Value  = '".jsEscapeString($this->getValue())."' ;oFCKeditor.Create() ;    </script>";


    /*    $fck = new FCKeditor($this->name);
        $fck->BasePath = $GLOBALS['FRAMEWORKDIR'].'/thirdParty/htmlEditor/fckeditor/';
        $fck->Width = '100%';
        $fck->Value = $this->getValue();
        $fck->Height = $this->heightInPixels;
        return $fck->CreateHtml();*/

    $toReturn = '';
    $toReturn .= '<script language="javascript" type="text/javascript">
  tinyMCE.init({
    mode: "exact",
    theme: "advanced",
    plugins: "table,style",
    theme_advanced_resizing : true,
    style_font:"Arial, Helvetica, sans-serif=Arial, Helvetica, sans-serif;Times New Roman, Times",
    theme_advanced_buttons1_add : "forecolor,backcolor,styleprops",
    theme_advanced_buttons3_add_before : "tablecontrols",
    theme_advanced_toolbar_location: "top",
    theme_advanced_statusbar_location : "bottom",
    elements : "'.$this->name.'"
  });
</script>';


    /*
        theme: "advanced",
        theme_advanced_toolbar_location: "top",
    theme_advanced_text_colors : "FF00FF,FFFF00,000000",
        theme_advanced_resizing: "true",
        theme_advanced_fonts : "Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace",

      */

    $toReturn .= ' <textarea  '.$this->getDisabledAttribute().' tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'" rows="'.$this->rows.'" cols="'.$this->cols.'" '.$this->attribute.' >';
    if (strlen($this->getValue() ?? '') > 0) {
      $toReturn .= $this->getValue(); // don't mess with data at all
    }
    $toReturn .= '</textarea>';
    return $toReturn;
  }


  function validate() {
    // call standard parent validation method (required field)
    // then call own validation on the length
    if (parent::validate($this->value)) {
      // manually check the string length in case the client browser
      // doesn't have javascript turned on.
      // if javascript is on, it will prevent user from typing when 
      // maxChars is reached
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
