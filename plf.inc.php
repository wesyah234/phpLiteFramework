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
// the name of the directory where project specific
// modules, blocks, etc are stored
//  test
// the php include search path must contain entries so that these directories
// can be found.  Generally, the project directory can be located by having "." in 
// the include path, and placing the project directory in the same directory
// as your main project website, and placing the starting index.php file there
// BTW, the starting index.php file should only need to contain 2 lines of PHP code:
//
//  require 'phpLiteFramework/plf.inc.php';
//  plfGo();
//
// first, check to see we're using php 5
// http://www.gophp5.org !!
//
$versionRequired = '5.0.0';
$versionRunning = phpversion();
if (version_compare(phpversion(), $versionRequired) < 0) {
  echo "The PHP Lite Framework requires at least PHP $versionRequired to run.<br/>";
  echo "You seem to be running version $versionRunning<br/>";
  echo "<br/><br/>If you are running in a mixed / shared hosting environment, you may be able to force PHP 5 by adding the following line to an .htaccess file:<br/><br/>";
  echo "<code>AddHandler application/x-httpd-php5 .php</code><br/><br/>";
  echo "The demo application contains a file named htaccessExample, which you may just rename to .htaccess<br/><br/>";
  echo "If you are exclusively running php 4, you will need to upgrade your php installation.<br/><br/>";
  echo 'For more information: <a href="http://gophp5.org" title="Support GoPHP5.org">
<img src="http://gophp5.org/sites/gophp5.org/buttons/goPHP5-100x33.png"
height="33" width="100" alt="Support GoPHP5.org" />
</a>';
  die();
}
// solution found at: https://www.popmartian.com/tipsntricks/2015/07/14/howto-use-php-getallheaders-under-fastcgi-php-fpm-nginx-etc/
//defines the getallheaders() method that may not be present if using php-fpm or other fastcgi methods of running PHP
if (!function_exists('getallheaders')) {
  function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }
}

// the framework directory is usually specified specifically in the include path
// for the entire system, so that multiple projects can share the same framework
// code.
//define('PROJECT_DIR', 'project');
//define('FRAMEWORK_DIR', 'phpLiteFramework');
//define('UTIL_DIR', FRAMEWORK_DIR.'/util');
//define('THIRDPARTY_DIR', FRAMEWORK_DIR.'/thirdParty');
// turn on all error reporting (development time only)
//error_reporting(E_ALL);
if (!function_exists('getServerName')) {
  function getServerName() {
    if (isset($_SERVER['HTTP_HOST'])) {
      $serverName = $_SERVER['HTTP_HOST'];
    }
    elseif (isset($_SERVER['HOSTNAME'])) {
      $serverName = $_SERVER['HOSTNAME'];
    }
    elseif (isset($_SERVER['COMPUTERNAME'])) {
      $serverName = $_SERVER['COMPUTERNAME'];
    }
    // just get first portion if it includes dots
    // so that a user going to testmachine.interaldomain.org
    // and a user going to testmachine
    // will both see testmachine as the SERVERNAME and thus
    // both load the correct config files using that servername
    // in the filename (config.servername.php)
    $dotPosition = strpos($serverName, '.');
    if ($dotPosition) {
      return substr($serverName, 0, $dotPosition);
    }
    else {
      return $serverName;
    }
  }

}

/**
 * Simple function to replicate PHP 5 behaviour.  Provides
 * fine grained access to the system time, for calculation
 * of execution times
 */
function microtime_float() {
  list($usec, $sec) = explode(" ", microtime());
  return ((float)$usec + (float)$sec);
}

/**
 * used to define a constant, only defines it if it's not already been defined
 */
function setDefault($constantName, $value) {
  if (!defined($constantName)) {
    define($constantName, $value);
  }
}

function plfIncludeStage1($projectDir) {
  // set any constants that the peoject specific settings didn't define
  require 'util/defaultconfig.inc.php';

  // PLF methods to support the framework
  require 'util/frameworkFunctions.php';

  // browser capabalities script
  require 'thirdParty/mobileesp/mdetect.php';

 // getting via composer now`
  // require 'thirdParty/date/Carbon-2.61.0/autoload.php';

  // only pull in the composer stuff if we're above php 7.1
  // right now the composer stuff is just the phpspreadsheet library
  $versionRequired = '7.1.0';
  $versionRunning = phpversion();
  if (version_compare(phpversion(), $versionRequired) >= 0) {
    require 'vendor/autoload.php';
  }
}

function plfIncludeStage2($projectDir) {
  // definitions of commonly used methods across the project
  $globalFuncsFile = $projectDir.'/project/conf/globalFunctions.php';
  if (file_exists($globalFuncsFile)) {
    include $globalFuncsFile;
  }

//  define('ADODB_DIR', dirname(__FILE__).'/thirdParty/dbAbstraction/adodb-5.22.2'); // adodb instructions require this
//  require ADODB_DIR.'/adodb.inc.php';

  // PLF designed wrapper functions around the ADOdb database abstraction library
  require 'util/dbFunctions.php';

  // a rounded border creator using CSS and not images
  require 'thirdParty/cssRoundedBorder/phpMyBorder2.class.php';
}

function plfGo($projectDir) {
  $start = microtime_float();
  plfIncludeStage1($projectDir);

  // fix some weird timezone issues that are cropping up with
// php5
  if (PHP_TZ_OVERRIDE_ONLY_IF_NOT_SET && empty(ini_get('date.timezone'))) {
    ini_set('date.timezone', PHP_TZ_OVERRIDE_ONLY_IF_NOT_SET);
  }
  if (PHP_TZ_OVERRIDE_ALWAYS) {
    ini_set('date.timezone', PHP_TZ_OVERRIDE_ALWAYS);
  }



// NOTE: we aren't including Zend Framework in the PHP Lite Framework anymore.
// Users of plf can now include just the Zend classes they need in the project/classes directory
// This autoloader will include them automatically, breaking them apart on the underscore
// as per the Zend Framework convention.
// Custom writen classes for the particular project can also be inclued in the project/classes
// directory and will automatically be included here
// If one needs to include something that doesn't work with this autoload logic
// one can put their own autoloader in their index.php and then call spl_autoload_register
  set_include_path(get_include_path().PATH_SEPARATOR.'project/classes');

  // NOTE: in 11/2020 (yes that year) we got a warning on one of our servers when upgrading
  // to php 7.3: " Deprecated: __autoload() is deprecated, use spl_autoload_register()
  // so, I just changed this function to another name, since I was already registereing the function
  // using spl_autolaod_register
//  function __autoload($className) {
  function plf_autoloader($className) {
    $path = "project/classes/".str_replace("_", "/", $className).".php";
    if (file_exists($path)) {
      include_once $path;
    }
  }

//  spl_autoload_register('__autoload');
  spl_autoload_register('plf_autoloader');

  setGlobalVar('PROJECT_DIR', $projectDir);

// if someone is calling this in command line mode, or they've set the 
// EMBED_FRAMEWORK_IN_ANOTHER_WEBAPP variable, just get out now... 
  // we've included the important code that they will need and 
  // we don't need to go any farther to parse urls, etc
  if (!isset($_SERVER['HTTP_HOST']) || EMBED_FRAMEWORK_IN_ANOTHER_WEBAPP) {
    // include the db stuff first, then get out
    plfIncludeStage2($projectDir);
    return;
  }
  // now, double check that someone's not in here via a call to another php script that is not
  // the index.php file (the central controller)
  // this could be a hack attempt, or an attempt to call one of the standalone scripts in the main
  // dir of the project that is intended to be run only interactively by someone on the server
  // NOTE: when we allow the user to set the script name (ie. to something different like index.jsp)
  // then we'll have to modify this check to use that variable instead.
  if (substr($_SERVER['SCRIPT_NAME'], strlen($_SERVER['SCRIPT_NAME'] ?? '') - strlen('index.php')) !== 'index.php') {
    $remoteAddr = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : 'Unknown';
    $requestUri = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : 'Unknown';
    logErrorSilent("A php script other than the main controller (index.php) is being called from a web environment, which is not allowed for security reasons. The request uri was: $requestUri The request is coming from remote ip address $remoteAddr - if you aren't expecting this, it may be an attempted security breach.  We will now halt execution of this script.");
    die();
  }

  // -----------------------------------------------------------
  // ------------------- OK, Here we go... ---------------------
  // -----------------------------------------------------------
  // force ssl mode if set in config file
  if (FORCESSL) {
    if (!isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) != 'on') {
      header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
      exit();
    }
  }

  // sitedown.txt is the default filename (see util/defaultconfig.inc.php)
  // if this file is present in the root of the web app, it will be shown instead of running 
  // the web app.
  $siteDownFileExists = file_exists(SITE_DOWN_FILENAME);
  if ($siteDownFileExists) {
    echo file_get_contents(SITE_DOWN_FILENAME);
    exit();
  }

  if (!isset($_REQUEST['module'])) {
    // module= not passed in, so assume we are doing pretty urls

    // add the request variables into the $_REQUEST array as they would have been if we were
    // using regular url params:
    // index.php?module=myMod&func=myFunc&param1=33&param2=34
    // from the positional /myMod/myFunc/param1/33/param2/34
    // but leave any $_REQUEST params already in there to save post parms.

    // clear out the request parm containing the slashes (leaving any other params we will want to keep)
    foreach ($_REQUEST as $key => $val) {
      $posOfSlash = strpos($key, '/');
      if ($posOfSlash !== false) {
        unset($_REQUEST[$key]);
      }
    }


    $prettyParams = explode('/', $_SERVER['QUERY_STRING']);
    // If url looks like this:
    // http://mysite.com/?mymodule/myfunc/parm1/value1&oldstyleparm=oldstylevalue
    // the last perttyparam will contain: value1&oldstyleparm=oldstylevalue
    // and to handle this we need to find the position of the ampersand and pull the extra off
    $positionOfAmpersand = strpos($prettyParams[count($prettyParams) - 1], "&");
    if ($positionOfAmpersand !== false) {
      // so just set the last $prettyParam equal to the stuff before the ampersand
      $prettyParams[count($prettyParams) - 1] = substr($prettyParams[count($prettyParams) - 1], 0, $positionOfAmpersand);
    }

    foreach ($prettyParams as $position => $value) {
      if (0 == $position) {
        $_REQUEST['module'] = $value;
      }
      elseif (1 == $position) {
        $_REQUEST['func'] = $value;
      }
      else {
        if ($position % 2 == 0) {
          // special handling here for array type parameters, as they come in with brackets
          // old way: &domains[]=7&domains[]=24
          // new way /domains[]/7/domains[]/24
          $posOfArrayIndicator = strpos($value, '[]');
          if ($posOfArrayIndicator === false) {
            if (isset($prettyParams[$position + 1])) {
              $_REQUEST[$value] = urldecode($prettyParams[$position + 1]);
            }
          }
          else {
            $paramName = substr($value, 0, $posOfArrayIndicator);
            $_REQUEST[$paramName][] = urldecode($prettyParams[$position + 1]);
          }
        }
      }
    }
  }

  //clean all input so modules don't have to :
  $_REQUEST = cleanArray($_REQUEST);
  $_GET = cleanArray($_GET);
  $_POST = cleanArray($_POST);
  $_COOKIE = cleanArray($_COOKIE);

  $module = null;
  $func = null;

  if (isset($_REQUEST['module']) && !is_array($_REQUEST['module'])) {
    $module = $_REQUEST['module'];
  }
  if (isset($_REQUEST['func']) && !is_array($_REQUEST['func'])) {
    $func = $_REQUEST['func'];
  }
  // save off the current arguments used... for use by getCurrentArgsArray()
  setCurrentArgsArray($_REQUEST);

  plfIncludeStage2($projectDir);
  // run the prefilter if the file exists
  // this is a good way to apply security at the module/function level
  // just return some message from the pre method, and it will
  // be displayed instead of calling the requested function
  $preFilterFilename = $projectDir.'/project/filters/pre.php';
  if (file_exists($preFilterFilename)) {
    include $preFilterFilename;
    $preFilterMsg = pre($module, $func);
  }

  // if the prefilter returned something display it, else
  // run the requested function
  if (!empty($preFilterMsg)) {
    $return = $preFilterMsg;
  }
  else {
//    pushRequestUrl(getPageTitle());

    if (empty($module)) {
      loadModuleFile($projectDir, DEFAULTMODULE);
      $return = callFunc(DEFAULTMODULE, DEFAULTFUNC);
    }
    elseif ('showStatusMsg' == $module) {
      // this "persistent" status message is propagated via cookie
      // to avoid using the session which would result in unncessary
      // session creation on the server in the case where sessions are not
      // explicitly being used.  didn't use url because this makes the url messy
      $return = getSessionVar('statusMsgSticky');
    }
    else {
      loadModuleFile($projectDir, $module);
      // see if user is requesting direct output via new method of altering the func name
      $directOutput = false;
      $modfunc = "{$module}_{$func}";
      if (function_exists($modfunc."_direct")) {
        $directOutput = true;
        $func = $func."_direct";
      }
      if ($directOutput) {
        // just call func directly and let it echo.. don't trap the echos
        //also, just exit here when done.
        callFuncNewDirect($module, $func);
        exit();
      }
      else {
        // buffer entire call:
        $return = callFunc($module, $func);
        if ($return == '' && !getDirectOutput()) {
          loadModuleFile($projectDir, DEFAULTMODULE);
          $return = callFunc(DEFAULTMODULE, DEFAULTFUNC);
        }
      }
      pushRequestUrl();
    }
  }

  // run the postfilter if the file exists
  // this is a good way to do something AFTER the module_function runs
  // anything returned from the post method will
  // be displayed instead of the returned data from the module_function call
  $postFilterFilename = $projectDir.'/project/filters/post.php';
  if (file_exists($postFilterFilename)) {
    include $postFilterFilename;
    $postFilterMsg = post($module, $func);
  }
  if (!empty($postFilterMsg)) {
    $return = $postFilterMsg;
  }

// future todo: 
// 6/2013: I added the ability to append _direct to the function name, as a new way of doing 
// 'direct output'... below is the old code that handles the template.  in the future, it would 
// be nice to modify this template handling code, so that we can stream out the first half of
// the template up to the {{body}} tag, then call the function, letting it echo, and finally
// handle the rest of the template after the body tag.  This would help the responsiveness on 
// big pages.  
// handle if setDirectOutput() is called inside the function (ie. the old way of doing it..
// new way is to use _direct at the end of the function name)
  if (getDirectOutput()) {
    echo $return;
    exit;
  }

  // process template, and build the whole page
  $contents = file_get_contents(getTemplate().'.html');

  // take care of the blocks
  $tokens = preg_match_all('/\[\[([a-z0-9]*)\]\]/i', $contents, $matches);
  $newStuff = $contents;
  foreach ($matches[1] as $match) {
    $newStuff = str_replace('[['.$match.']]', callBlock($projectDir, $match) ?? '', $newStuff);
  }

  $pageTitle = getPageTitle() ?? '';

  // take care of the page title in the template (generally displayed above the body)
  $newStuff = str_replace('{{pageTitle}}', $pageTitle, $newStuff);

  // take care of the meta description and keywords in the template (generally displayed above the body)
  $newStuff = str_replace('{{metaDescription}}', getMetaDescription() ?? '', $newStuff);
  $newStuff = str_replace('{{metaKeywords}}', getMetaKeywords() ?? '', $newStuff);

  // take care of the page title with the site name appended (generally used for the <title> attribute
  // of the html pages (here we strip html tags for clarity)
  if (isset($pageTitle)) {
    $pageTitleWithSiteName = WEBSITENAME.' - '.$pageTitle;
  }
  else {
    $pageTitleWithSiteName = WEBSITENAME;
  }
  $newStuff = str_replace('{{pageTitleWithSiteName}}', strip_tags($pageTitleWithSiteName) ?? '', $newStuff);
  $newStuff = str_replace('{{siteName}}', strip_tags(WEBSITENAME) ?? '', $newStuff);

  $headContent = '';
  $frameworkUrl = getFrameworkUrl();

  // grab scriptaculous/prototype JS:
  if (USE_PROTOTYPE_SCRIPTACULOUS) {
    $headContent .= '<script src="'.$frameworkUrl.'/thirdParty/scriptaculous/scriptaculous-js-1.9.0/lib/prototype.js" type="text/javascript"></script><script src="'.$frameworkUrl.'/thirdParty/scriptaculous/scriptaculous-js-1.9.0/src/scriptaculous.js" type="text/javascript"></script>';
  }
  if (USE_JQUERY) {
    //$headContent .= '<script src="'.$frameworkUrl.'/thirdParty/jquery/jquery-1.12.4.js" type="text/javascript"></script><script src="'.$frameworkUrl.'/thirdParty/jquery/jquery-ui-1.12.1.custom.min.js" type="text/javascript"></script><script>jQuery.noConflict()</script>';

    $headContent .= '<script src="'.$frameworkUrl.'/thirdParty/jquery/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>';
  }
  if (getGlobalVar('usingDropzone')) {
    setHeadContent('<script type="text/javascript" src="'.getFrameworkUrl().'/thirdParty/dropzone/dropzone.js'.'"></script>');
    setHeadContent('<link href="'.getFrameworkUrl().'/thirdParty/dropzone/basic.css" type="text/css" rel="stylesheet">');
    setHeadContent('<link href="'.getFrameworkUrl().'/thirdParty/dropzone/dropzone.css" type="text/css" rel="stylesheet">');
  }

  // reference the couple of helper js funcs defined in the framework:
  // note that some/all of these functions need jquery to be included, so don't turn
  // it off by setting USE_JQUERY to false if you intend to use the functions in plf.js
  $headContent .= '<script src="'.$frameworkUrl.'/util/javascript/plf.js" type="text/javascript"></script>';
  // copy to clipboard functionality from (https://clipboardjs.com/)
  $headContent .= '<script src="'.$frameworkUrl.'/thirdParty/clipboardjs/clipboard.min.js" type="text/javascript"></script>';
  // NOTE: had to move this cool calendar javascript loading to the head of the whole page so that if we had a 
  // // date control on a form that was pulled via ajax it would still work and have the JS available.
  // background grey stuff below is a hack way to fix the calendar.. in chrome on mac, the calendar month is black words on black background!  change the background to grey explicitly to fix for now.. later, maybe switch to a jquery date popup or some other more modern date picker
  $headContent .= '
<style type="text/css">@import url('.$frameworkUrl.'/thirdParty/dhtmlCalendar/jscalendar-1.0/calendar-system.css'.');</style>
  
  <style>.calendar thead .title {
    background: grey;
}</style>
<script type="text/javascript" src="'.$frameworkUrl.'/thirdParty/dhtmlCalendar/jscalendar-1.0/calendar.js'.'"></script><script type="text/javascript" src="'.$frameworkUrl.'/thirdParty/dhtmlCalendar/jscalendar-1.0/lang/calendar-en.js'.'"></script><script type="text/javascript" src="'.'project/conf/calendar-setup.js'.'"></script>';

  // set up a style on the acronym tag since IE doesn't apply the dotted underline
  //to the <acronym> tagged page elements, like FF does...
  // and also set the plf_status_red css style that is now used instead of hard coding the red style for the status message
  // this will allow projects to use a different css style for the status message while still getting the default
  // red behavior.
  $headContent .= '<style type="text/css">acronym{border-bottom: #000 1px dotted} .plf_status_red {color:red;}</style>';

  if (1 == getGlobalVar('usingOverlib')) {
    $headContent .= '<script type="text/javascript" src="'.$frameworkUrl.'/thirdParty/overlib/overlib/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>';
  }
  $headContent .= getHeadContent();
  $newStuff = str_replace('{{headContent}}', $headContent ?? '', $newStuff);

  // do the body attribute

  $newStuff = str_replace('{{bodyAttribute}}', getBodyAttribute() ?? '', $newStuff);

  // take care of the transient status message:
  $statusMsg = getSessionVar('statusMsg');
  if (isset($statusMsg)) {
    delSessionVar('statusMsg');
    $newStuff = str_replace('{{statusMessage}}', '<div class="'.STATUS_MESSAGE_DIV_CLASS.'">'.$statusMsg.'</div>', $newStuff);
  }
  else {
    $newStuff = str_replace('{{statusMessage}}', '', $newStuff);
  }

  // take care of the main body
  $newStuff = str_replace('{{body}}', $return ?? '', $newStuff);

  $end = microtime_float();
  $took = $end - $start;

  echo  "<!-- page built in $took seconds -->";
  echo $newStuff;
  echo  "<!-- page built in $took seconds -->";

}

?>
