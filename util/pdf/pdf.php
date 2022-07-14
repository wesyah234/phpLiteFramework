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
require(FRAMEWORKDIR.'/thirdParty/pdfGeneration/fpdf/fpdf.php');

class PDF extends FPDF {
  var $B;
  var $I;
  var $U;
  var $HREF;

  function PDF($orientation = 'P', $unit = 'mm', $format = 'letter') {
    //Call parent constructor
    $this->FPDF($orientation, $unit, $format);
    //Initialization
    $this->B = 0;
    $this->I = 0;
    $this->U = 0;
    $this->HREF = '';
  }

  function WriteHTML($h, $html) {
    //HTML parser
//    $html=str_replace("\n",' ',$html);
// fix below along with other fix mentioned a few lines down 
// takes care of an issue where subsequent paragraphs were getting 
// indented by one space, so changed it to first look for \r to replace
// with space, then later, I search for a space followed by a newline and
// replace it by nothing... not enough time to figure this out
// Wes Rood, 11/17/2004
    $html = str_replace("\r", ' ', $html);
    $a = preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($a as $i => $e) {
      if ($i % 2 == 0) {
        //Text
        if ($this->HREF)
          $this->PutLink($this->HREF, $e);
        else
          //$this->Write($h,$e);
// rest of the fix is here:
          $this->Write($h, str_replace(" \n", '', $e));
      }
      else {
        //Tag
        if ($e{0} == '/')
          $this->CloseTag(strtoupper(substr($e, 1)));
        else {
          //Extract attributes
          $a2 = explode(' ', $e);
          $tag = strtoupper(array_shift($a2));
          $attr = array();
          foreach ($a2 as $v)
            if (ereg('^([^=]*)=["\']?([^"\']*)["\']?$', $v, $a3))
              $attr[strtoupper($a3[1])] = $a3[2];
          $this->OpenTag($tag, $attr);
        }
      }
    }
  }

  function OpenTag($tag, $attr) {
    //Opening tag
    if ($tag == 'B' or $tag == 'I' or $tag == 'U')
      $this->SetStyle($tag, true);
    if ($tag == 'A')
      $this->HREF = $attr['HREF'];
    if ($tag == 'BR')
      $this->Ln();
  }

  function CloseTag($tag) {
    //Closing tag
    if ($tag == 'B' or $tag == 'I' or $tag == 'U')
      $this->SetStyle($tag, false);
    if ($tag == 'A')
      $this->HREF = '';
  }

  function SetStyle($tag, $enable) {
    //Modify style and select corresponding font
    $this->$tag += ($enable ? 1 : -1);
    $style = '';
    foreach (array('B', 'I', 'U') as $s)
      if ($this->$s > 0)
        $style .= $s;
    $this->SetFont('', $style);
  }

  function PutLink($URL, $txt) {
    //Put a hyperlink
    $this->SetTextColor(0, 0, 255);
    $this->SetStyle('U', true);
    $this->Write(5, $txt, $URL);
    $this->SetStyle('U', false);
    $this->SetTextColor(0);
  }
}
