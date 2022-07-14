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

class PLF_MultipleFileUpload extends PLF_Element
{
  var $maxFileSizeBytes;

  function __construct($name, $label, $maxFileSizeBytes, $required) {
    PLF_Element::PLF_Element($name, $label, $required);
    $this->maxFileSizeBytes = $maxFileSizeBytes;
  }

  function PLF_MultipleFileUpload($name, $label, $maxFileSizeBytes, $required) {
    self::__construct($name, $label, $maxFileSizeBytes, $required);
  }

  // MyForm class will set tabindex before calling this method
  function render($tabIndex) {
    $toReturn = ' <input  '.$this->getDisabledAttribute().' type="file" tabindex="'.$tabIndex.'" name="'.$this->getName().'[]" id="'.$this->getName().'"';
    $toReturn .= ' '.$this->attribute.' multiple="" />';
    return $toReturn;
  }

  // this method rearranges the $_FILES array to be easier to work with
  function getFilesArray() {
    $arrayToReturn = array();
    $fileKeys = array_keys($_FILES[$this->getName()]);
    for ($i = 0; $i < count($_FILES[$this->getName()]['name']); $i++) {
      $currentFile = array();
      foreach ($fileKeys as $fileKey) {
        $currentFile[$fileKey] = $_FILES[$this->getName()][$fileKey][$i];
      }
      $arrayToReturn[] = $currentFile;
    }
    return $arrayToReturn;
  }

  function validate() {
    // NOTE: this logic adapted from the single FileUpload.php class

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
    $filesArray = $this->getFilesArray();
    foreach ($filesArray as $currentFile) {
      // extract the filename for the error messages
      $filename = $currentFile['name'];
      // now validate it:
      if ($currentFile['size'] > $this->maxFileSizeBytes) {
        $this->requiredText .= "$filename: the file size is larger than the maximum size of ".displayFilesize($this->maxFileSizeBytes).' ';
        $valid = false;
      }
      elseif ($this->required && $currentFile['error'] == UPLOAD_ERR_NO_FILE) {
        $this->requiredText .= 'this field is required ';
        $valid = false;
      }
      elseif ($currentFile['error'] != 0 && $currentFile['error'] != UPLOAD_ERR_NO_FILE) {
        $codes = getFileUploadErrorDescriptions();
        $this->requiredText .= "$filename: ".$codes[$currentFile['error']].' ';
        $valid = false;
      }
      else {
        $valid = true;
      }
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
