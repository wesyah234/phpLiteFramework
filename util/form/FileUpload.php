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

class PLF_FileUpload extends PLF_Element {
  var $maxFileSizeBytes;

  function __construct($name, $label, $maxFileSizeBytes, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->maxFileSizeBytes = $maxFileSizeBytes;
  }

  function PLF_FileUpload($name, $label, $maxFileSizeBytes, $required) {
    self::__construct($name, $label, $maxFileSizeBytes, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = ' <input  '.$this->getDisabledAttribute().' type="file" tabindex="'.$tabIndex.'" name="'.$this->getName().'" id="'.$this->getName().'"';
    $toReturn .= ' '.$this->attribute.' />';
    return $toReturn;
  }

  function validate() {
    // what you see below is an attempt to handle both cases:
    // 1. the file upload is required
    // 2. the file upload is not required

    // if it's required, we validate to the max
    // if it's not required, but the user provides a file, 
    // then we can validate against max size, and also check the other error
    // codes that may have been set.

    // if you are saving the file data in the database (the default behavior
    // at this point), and taking advantage of the build in getFieldsForUpdate, etc
    // methods, then you must (at this point)  
    // take care of removing the field before generating an update
    // statement in the case where the file argument is not required and 
    // it is not provided (this is to prevent the file data in the db from getting
    // nulled out)

    $theFile = $_FILES[$this->getName()];
    $valid = false;
    if ($this->required && empty($theFile['tmp_name'])) {
      $this->requiredText = 'this field is required';
    }
    elseif ($this->required && $theFile['size'] == 0) {
      $this->requiredText = 'filesize is 0, try again';
    }
    elseif ($theFile['size'] > $this->maxFileSizeBytes) {
      $this->requiredText = 'the file size is larger than the maximum size of '.displayFilesize($this->maxFileSizeBytes);
    }
    elseif ($this->required && $theFile['error'] == UPLOAD_ERR_NO_FILE) {
      $this->requiredText = 'this field is required';
    }
    elseif ($theFile['error'] != 0 && $theFile['error'] != UPLOAD_ERR_NO_FILE) {
      $codes = getFileUploadErrorDescriptions();
      $this->requiredText = $codes[$theFile['error']];
    }
    else {
      $valid = true;
    }

    return $valid;
  }

  function getValue() {
    return $this->getValue();
  }

  function getValueForDb() {
    return fread(fopen($_FILES[$this->getName()]['tmp_name'], "r"), $_FILES[$this->getName()]['size']);
  }
}

?>
