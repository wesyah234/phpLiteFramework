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

/**
 * Handy functions to wrap the ADOdb database abstration library
 * http://adodb.sourceforge.net/
 *
 *
 * TODO:
 * wrap ADODB's support for transactions:
 * http://phplens.com/adodb/tutorial.smart.transactions.html
 *
 */
//putenv('ORACLE_HOME='.ORACLE_HOME); // this allows it to look up the oracle error msg
//define('ADODB_DIR', '../thirdParty/dbAbstraction/adodb'); // adodb instructions require this
//require ADODB_DIR.'/adodb.inc.php';
// uncomment if using the adodb provided "pager" for long db results
//require ADODB_DIR.'/adodb-pager.inc.php';
// this is for a mysql timestamp column
// 20050324100917
function parseTimestamp($timedate) {
  $year = substr($timedate ?? '', 0, 4);
  $month = substr($timedate ?? '', 4, 2);
  $day = substr($timedate ?? '', 6, 2);
  $hour = substr($timedate ?? '', 8, 2) + pnSessionGetVar('timeoffset');
  $min = substr($timedate ?? '', 10, 2);
  $sec = substr($timedate ?? '', 12, 2);
  return date('m/j/y g:i a', mktime($hour, $min, $sec, $month, $day, $year));
}

// this is for a mysql datetime column
// 2005-03-03 15:05:06
function parseDatetime($timedate) {
  $year = substr($timedate ?? '', 0, 4);
  $month = substr($timedate ?? '', 5, 2);
  $day = substr($timedate ?? '', 8, 2);
  $hour = substr($timedate ?? '', 11, 2) + pnSessionGetVar('timeoffset');
  $min = substr($timedate ?? '', 14, 2);
  $sec = substr($timedate ?? '', 17, 2);
  return date('m/j/y g:i a', mktime($hour, $min, $sec, $month, $day, $year));
}

/**
 * prepares the sql statement, precompiling it if the db supports it
 * (ie. for oracle, use :field1, :field2, etc as placeholders)
 *
 * follow this with executePrepared()
 *
 * $sql="insert into tablename (field1, field2) values (:myfield1,:myfield2)";
 * $preparedInsert=prepare($sql);
 *
 * $values = array();
 * $values['myfield1'] = 5;
 * $values['myfield2'] = "test data";
 *
 * executePrepared($preparedInsert, $values);
 *
 *
 *
 *
 */
function prepare($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  $result = $dbconn->Prepare($sql);
  checkError($dbconn);
  return $result;
}

function getSeq($seqName, $dbname = NULL) {
  return fetchField("select $seqName.nextval from dual", $dbname);
}

/**
 * Override the default fetch mode and switch to
 * NUMERIC (ie. ADODB_FETCH_NUM)
 *
 *          [0] => 49
 * [2] => 294
 * [3] => 12/04/1996
 *
 * instead of:
 *
 * [SESS_ID] => 49
 * [SUBJECT_ID] => 294
 * [DATE] => 12/04/1996
 */
function setNumFetchMode($dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  $dbconn->setFetchMode(ADODB_FETCH_NUM);
}

/**
 * Override the current fetch mode and switch to ASSOCIATIVE
 * (ie. ADODB_FETCH_ASSOC)
 *
 * [SESS_ID] => 49
 * [SUBJECT_ID] => 294
 * [DATE] => 12/04/1996
 *
 * instead of:
 *
 *          [0] => 49
 * [2] => 294
 * [3] => 12/04/1996
 */
function setAssocFetchMode($dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  $dbconn->setFetchMode(ADODB_FETCH_ASSOC);
}

function plfConstant($constantName) {
  if (defined($constantName)) {
    $theValue = constant($constantName);
  }
  else {
    $theValue = getArrayValueAtIndex($_ENV, $constantName);
  }
  return $theValue;
}

function &getNamedConnection($connectionName = NULL) {
  $connectionNameOrig = $connectionName;
  if ($connectionName != NULL) {
    $connectionName .= '_';
  }
  if (plfConstant($connectionName.'ADODBDRIVER') == NULL || plfConstant($connectionName.'DBHOSTNAME') == NULL || plfConstant($connectionName.'DBUSERNAME') == NULL || plfConstant($connectionName.'DBPASSWORD') == NULL || plfConstant($connectionName.'DBNAME') == NULL) {
    if (isset($connectionName)) {
      logError("You are attempting to use a database named $connectionNameOrig, but the database connection settings for this database are not defined.  To use this database, you must define 5 constants named after this connection name: {$connectionName}ADODBDRIVER, {$connectionName}DBHOSTNAME, {$connectionName}DBNAME, {$connectionName}DBUSERNAME, and {$connectionName}DBPASSWORD. These constants must be defined in the index.php file or provided as system environment variables, prior to calling plfGo().");
      die();
    }
    else {
      logError("You are attempting to use a database function but the database connection settings are not defined.  To use the database functions, you must define 5 constants: ADODBDRIVER, DBHOSTNAME, DBNAME, DBUSERNAME, and DBPASSWORD. These constants must be defined in the index.php file or provided as system environment variables, prior to calling plfGo().");
      die();
    }
  }

  // this is not heavily commented in order to protect projects using this code
  // from search engines that choose to index source code from open source
  // projects
  // -----
  // if you don't understand what it's doing, don't bother
  //  
  $p = plfConstant($connectionName.'DBPASSWORD');
  if ($p == NULL) {
    $ep = plfConstant($connectionName.'DBPASSWORD_EP');
    $ky = plfConstant($connectionName.'DBPASSWORD_KY');
    $alg = plfConstant($connectionName.'DBPASSWORD_ALG');
    if (isset($ep) && isset($ky) && isset($alg)) {
      $p = $alg($ep, $ky);
    }
    else {
      logError("No password set for connection named <$connectionName> and unable to determine password using alternate method.  Please check your database settings.");
    }
  }

  return getConnection(plfConstant($connectionName.'DBUSERNAME'), $p, plfConstant($connectionName.'DBNAME'), plfConstant($connectionName.'DBHOSTNAME'), plfConstant($connectionName.'ADODBDRIVER'), plfConstant($connectionName.'DB_USE_SID'), plfConstant($connectionName.'DBCHARSET'), plfConstant($connectionName.'CONNECTIONPARAMETERARRAY'));
}

function oracleSysdate() {
  // this is the php equivalent of  
// $theConnection->NLS_DATE_FORMAT = 'MM/DD/YYYY';
// which is set in getConnection
  return date('m/d/Y');
}

/**
 * Convert the given date string to a date string using the framework
 * default of MM/DD/YYYY for the oracle default date format.
 *
 * The input string can be in any format that PHP's strtotime
 * method likes.
 * see:
 * http://us2.php.net/strtotime
 * and:
 * http://www.gnu.org/software/tar/manual/html_node/tar_109.html
 *
 */
function convertToOracleDate($date) {
  // this is the php equivalent of  
// $theConnection->NLS_DATE_FORMAT = 'MM/DD/YYYY';
// which is set in getConnection
  if (!empty($date)) {
    return (date("m/d/Y", strtotime($date)));
  }
}

function &getConnection($dbusername = DBUSERNAME, $dbpassword = DBPASSWORD, $dbname = DBNAME, $dbhostname = DBHOSTNAME, $adodbdriver = ADODBDRIVER, $useSid = DB_USE_SID, $charset = DBCHARSET, $connectionParameterArray = CONNECTIONPARAMETERARRAY) {
  $globalsIndexName = $dbusername.'|'.$dbname.'|'.$dbhostname.'|'.$adodbdriver;

  // this will give us a 40 second window of retry attempts before failing out
  $numTries = 10;
  $secondsSleepBetweenTries = 4;

  if (DEMO_MODE) {
    logWarning('Demo mode is on.  Only use DEMO_MODE when you are not doing database operations');
  }

  // keep as many connection objects in the $globals array as we are called upon to declare
  // ie, getConnection('username1', 'password1');
  // and
  // getConnection('username2', 'password2');
  // would create 2 connections in the globals array
  if (isset($GLOBALS[$globalsIndexName])) {
    $theConnection = &$GLOBALS[$globalsIndexName];
  }

  if (isset($theConnection)) {
    return $theConnection;
  }
  else {
    $theConnection = NewADOConnection($adodbdriver);
    if (!$theConnection) {
      // there was a problem constructing the connection object presumably due to problems in the config
      // file
      logError("Problem creating the database connection with hostname: $dbhostname, username: $dbusername, db name: $dbname, driver name: $adodbdriver, please double check the config file settings for the database");
    }
    // force indexing by the field names, override this using setNumFetchMode()
    $theConnection->SetFetchMode(ADODB_FETCH_ASSOC);

    // this might be necessary to set UTF8 encoding and other db parameters per:
    // https://adodb.org/dokuwiki/doku.php?id=v5:reference:connection:setconnectionparameter
    if (isReallySet($connectionParameterArray)) {
      foreach ($connectionParameterArray as $key=>$value) {
        $theConnection->setConnectionParameter($key, $value);
      }
    }

    // only for oracle dbs, SID or SERVICE NAME may be specified
    // originally just supported SERVICE NAME, but now this flag can be set
    // causing it to be able to log in based on a dbname that is actually the SID
    if ($useSid) {
      $theConnection->connectSID = true;
    }

    // below is oracle specific stuff for dealing with dates:
    // Oracle provides the NLS_DATE_FORMAT session setting that
    // will set the default date format for all inbound and outbound
    // date formats
    // In defaultconfig.inc.php we set this to MM/DD/YYYY to match 
    // the date popups used in the framework, and to allow users to see and type
    // mm/dd/yyyy for their dates in forms. However, if you want another default
    // format just change it like this in your index.php or other config file
    // define('DB_ORACLE_NLS_DATE_FORMAT', 'YYYY-MM-DD');
    // Regardless of this setting, you may retrieve dates and times in a different format by
    // using the to_char function in your select statements.
    if (property_exists($theConnection, 'NLS_DATE_FORMAT')) {
      $theConnection->NLS_DATE_FORMAT = DB_ORACLE_NLS_DATE_FORMAT;
    }
    
    // see documentation in defaultconfig.inc.php for the following setting:
    $GLOBALS['ADODB_COUNTRECS'] = DB_ADODB_COUNTRECS;
    // this call may trigger a warning
    if (USE_PERSISTENT_DB_CONNECT) {
      $return = $theConnection->PConnect($dbhostname, $dbusername, $dbpassword, $dbname);
    }
    else {
      $return = $theConnection->Connect($dbhostname, $dbusername, $dbpassword, $dbname);
    }
    $tries = 0;
    while ((!$return || checkDbErrorNo($theConnection->ErrorNo())) && $tries < $numTries) {
      $tries += 1;
      // don't log msg or sleep first time through, since most times we're able to reconnect
      // first time after a failure, and don't want to have to wait the full
      // second or display any msg
      if ($tries > 1) {
        logWarning('xxunable to connect to db --  Sleeping '.$secondsSleepBetweenTries.' seconds, then will try again, remaining tries: '.($numTries - $tries).' -- ErrorNo: '.$theConnection->ErrorNo().' ErrorMsg: '.$theConnection->ErrorMsg());
        sleep($secondsSleepBetweenTries);
      }
      // attempt to clear the apc cache, if we're using APC:
      clearPhpCache();
      // sleep 1 second, just to give it a sec to breathe...
      sleep(1);
      $theConnection = NewADOConnection($adodbdriver);
      if (!$theConnection) {
        // there was a problem constructing the connection object presumably due to problems in the config
        // file
        logError("Problem creating the database connection, please check other warnings and double check the config file settings for the database");
      }
      $theConnection->NLS_DATE_FORMAT = 'MM/DD/YYYY';
      if (USE_PERSISTENT_DB_CONNECT) {
        $return = $theConnection->PConnect($dbhostname, $dbusername, $dbpassword, $dbname);
      }
      else {
        $return = $theConnection->Connect($dbhostname, $dbusername, $dbpassword, $dbname);
      }
    }
    if (!$return) {
      $message = "Unable to connect to db (host: $dbhostname user: $dbusername name: $dbname), tried $tries times -- ErrorNo: ".$theConnection->ErrorNo().' ErrorMsg: '.$theConnection->ErrorMsg();
      logError($message);
      die();
    }
    else if (checkDbErrorNo($theConnection->ErrorNo())) {
      $message = "pconnect (host: $dbhostname user: $dbusername name: $dbname) continued to set non zero error num, even after ".$tries.' tries -- ErrorNo: '.$theConnection->ErrorNo().' ErrorMsg: '.$theConnection->ErrorMsg();
      logError($message);
      die();
    }
    else if ($tries > 1) {
      logWarning('successfully connected after failing '.$tries.' time(s)');
    }
    else {
//      trigger_error('dont log this , success on first connect', E_USER_WARNING);
    }

    // deal with character settings at the php level if necessary
    if ($charset) {
      //$theConnection->charSet = $charset;
      $theConnection->setCharSet($charset);
    }


    $theConnection->debug = DB_DEBUG_ON;
    $GLOBALS[$globalsIndexName] = &$theConnection;
    return $theConnection;
  }
}

/**
 * Utilizes the adodb library to properly quote and escape the $value
 * so that anything can be inserted into the db, including special characters
 * that happen to be used as special delimiters on the insert by the db.
 *
 * For example, the single quote is typically use to enclose strings, therefore
 * if it is part of the data you want to insert, it must be escaped.
 *
 * The methods below that form insert/update strings from the Form object
 * (getFieldsForInsert() , getFieldsForUpdate(), etc...)
 * will all call this method when building the strings, so the only time
 * you need to call this directly is if you are forming statements directly
 * without the help of the Form object
 *
 */
function dbQuoteString($value, $dbname = NULL) {
  if (DEMO_MODE) {
    return "'$value'";
  }
  else {
    $dbconn = getNamedConnection($dbname);
    return $dbconn->qstr($value);
  }
}

function dbQuoteStringWithNull($value, $dbname = NULL) {
  if (DEMO_MODE) {
    return "'$value'";
  }
  else {
    $dbconn = getNamedConnection($dbname);
    if ('' === $value || null === $value) {
      return 'null';
    }
    else {
      return $dbconn->qstr($value);
    }
  }
}

/**
 * Given an array of fields, performs a dbQuoteString operation
 * on each field, altering the array to contain the db quoted
 * version of each value
 */
function dbQuoteStrings(&$values, $dbname = NULL) {
  foreach ($values as $key => $value) {
    $values[$key] = dbQuoteString($value, $dbname);
  }
  return ($values);
}

function dbQuoteStringsWithNull(&$values, $dbname = NULL) {
  foreach ($values as $key => $value) {
    $values[$key] = dbQuoteStringWithNull($value, $dbname);
  }
  return ($values);
}


function dbConvertZeroDatesToEmptyStrings(&$values, $dbname = NULL) {
  foreach ($values as $key => $value) {
    if ('0000-00-00 00:00:00' == $value || '0000-00-00' == $value) {
      $values[$key] = '';
    }
  }
  return ($values);
}


/**
 * Perform a dbQuoteString operation on every field of the provided
 * array of values.  Additionally this function will trim the values.
 *
 * @param Array $values the array of values to work with (it is modified)
 * @param String $dbname optional name of database connection
 * @return Array the modified array (the array passed in is also modified)
 */
function dbQuoteStringsAndTrim(&$values, $dbname = NULL) {
  foreach ($values as $key => $value) {
    $values[$key] = dbQuoteString(trim($value), $dbname);
  }
  return ($values);
}

/**
 * Checks for a non zero error number and outputs the error message
 * and the error number, then dies :)
 *
 */
function checkError($dbconn) {
  if (checkDbErrorNo($dbconn->errorNo())) {
    trigger_error('dbprovider: '.$dbconn->dataProvider.' dbtype: '.$dbconn->databaseType.' dbhost: '.$dbconn->host.' db: '.$dbconn->database.' dbuser: '.$dbconn->user.' dberrorMsg: '.$dbconn->errorMsg().' dberrorNo: '.$dbconn->errorNo(), E_USER_ERROR);
    die();
  }
}

/**
 * Some dbs can return 0 for success, some can return "" for success, this checks for both
 */
function checkDbErrorNo($errorNo) {
  return ($errorNo !== '' && $errorNo !== 0);
}

/**
 * Use this to run db statements and ignore any error that comes back.
 * Use sparingly, perhaps for a situation where you don't want to bother
 * checking for a dupliate row and instead want to rely on the database's
 * constraint to prevent dups.  In this case you'd fire off the insert
 * without first checking if it's there. The db would allow the initial one,
 * and give a constraint violation on subsequent ones, but it would be ignored
 * with this function.
 *
 * Of course, this also prevents you from knowing that the db is down, but
 * the idea is that there are enough other db calls going on in the application
 * so you would know soon enough that there is a db problem.
 *
 * Future enhancement would be to discern which db errors were constraint type
 * violations and which were more serious database connection errors, and only
 * ignore the less serious ones...
 *
 * NOTE: the adodb framework will log all db errors, so set SHOWEVERYTHINGELSETOUSER
 *  to false to prevent a message from being echoed.
 */
function executeIgnoreError($statement, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->execute($statement);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $rs;
}

function selectLimit($statement, $limit, $offset, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->selectLimit($statement, $limit, $offset);
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $rs;
}

/**
 * Execute statement and return result set
 * (connection optional)
 *
 * also used in place of fetchRecords when we have a huge
 * result set and need to iterate over it via the resultset -
 * fetchRecords, since it fills up an array with the result,
 * cannot be used with huge result sets that are too big
 * to be stored in local memory.
 *
 * Usage:
 * $rs = executeQuery("select * from $tableName");
 * while (!$rs->EOF) {
 * extract($rs->fields);
 * //do something with the fields
 * $rs->moveNext();
 * }
 *
 */
function executeQuery($statement, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->execute($statement);
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $rs;
}

/**
 * Execute statement and return count of rows
 * updated (connection optional)
 *
 */
function executeUpdate($statement, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $dbconn->execute($statement);
  checkError($dbconn);
  $rows = $dbconn->affected_rows();
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $rows;
}

/**
 * Execute statement (connection optional)
 *
 * This function returns nothing.  If you want
 * a result set returned, use executeQuery().
 * If you want the count of the rows updated,
 * use executeUpdate().  If you could care less,
 * go ahead and use this one.
 */
function execute($statement, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $dbconn->execute($statement);
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
}

/**
 * Execute statement and return result set
 * (connection optional) see also: prepare()
 *
 *
 * $sql="insert into tablename (field1, field2) values (:myfield1,:myfield2)";
 * $preparedInsert=prepare($sql);
 *
 * $values = array();
 * $values['myfield1'] = 5;
 * $values['myfield2'] = "test data";
 *
 * executePrepared($preparedInsert, $values);
 *
 *
 */
function executePrepared($statement, $fields, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->execute($statement, $fields);
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $rs;
}

/**
 * Fetches multiple records from the database, moving them all into memory
 * and returing them as an array (associative) Uses $limit and $offset
 * to ease paging through large results
 */
function fetchRecordsLimit($sql, $limit, $offset, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->SelectLimit($sql, $limit, $offset);
  checkError($dbconn);
  $items = $rs->GetRows();
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $items;
}

function reportTiming($start) {
  static $accumulatedTime;
  static $max;
  static $min;
  static $allValues;
  $duration = microtime_float() - $start;
  if (isset($accumulatedTime)) {
    // append duration to accumulated time:
    $accumulatedTime += $duration;
    $allValues[] = $duration;
    $max = max($allValues);
    $min = min($allValues);
  }
  else {
    // initialize accumulated time with current duration:
    $accumulatedTime = $duration;
    $allValues = array($duration);
    $max = $min = $duration;
  }
  $total = count($allValues);
  $isMax = ($max == $duration) ? " * * CURRENT MAX * * " : "";
  $dt = date("Y-m-d H:i:s O");
  echo "$dt db statement $total took ".number_format($duration, 4)." seconds  (accumulated time: ".number_format($accumulatedTime, 4)." seconds, min: ".number_format($min, 4).", max: ".number_format($max, 4).") $isMax\n<br/>";
}

/**
 * Fetches multiple records from the database, moving them all into memory
 * and returing them as an array (associative)
 */
function fetchRecords($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->Execute($sql);
  checkError($dbconn);
  $items = $rs->GetRows();
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $items;
}

/**
 * Fetches multiple records from the database, returning an xml
 * string holding the results.  Example:
 * <rows>
 *   <row><field1>data</field1><field2>data2</field2></row>
 *   <row><field1>data abc</field1><field2>data2 abcde</field2></row>
 * </rows>
 *
 */
function fetchRecordsAsXml($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->Execute($sql);
  checkError($dbconn);
  $xmlStr = '';
  while (!$rs->EOF) {
    $xmlStr .= '<row>';
    for ($i = 0; $i < $rs->_numOfFields; $i++) {
      list($field, $value) = each($rs->fields);
      $xmlStr .= "<$field>";
      $xmlStr .= htmlspecialchars($value);
      $xmlStr .= "</$field>";
    }
    $rs->moveNext();
    $xmlStr .= '</row>';
  }
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return "<rows>$xmlStr</rows>";
}

function fetchRecordAsXml($sql, $dbname = NULL) {
  $array = fetchRecord($sql, $dbname);
  $xmlStr = '';
  $xmlStr .= "<row>";
  foreach ($array as $field => $value) {
    $xmlStr .= "<$field>".htmlspecialchars($value)."</$field>";
  }
  $xmlStr .= "</row>";
  return $xmlStr;
}

function createXmlFromRecord($record) {
  $xmlStr = '';
  foreach ($record as $field => $value) {
    $xmlStr .= "<$field>".htmlspecialchars($value)."</$field>";
  }
  return $xmlStr;
}

/**
 * Fetches a single record from the database, moving it into memory
 * and returing it as an array (associative)
 */
function fetchRecord($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $item = $dbconn->GetRow($sql);
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $item;
}

/**
 * Fetch a single field from a query (connection optional)
 * ex:
 * $maxYear = fetchField('select max(year) from tableA');
 */
function fetchField($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $field = $dbconn->GetOne($sql);
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $field;
}

/**
 * Gets the last insert id, (mysql has this)
 */
function getLastInsertId($dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $item = $dbconn->GetOne('select last_insert_id()');
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $item;
}

/**
 * Build an array that can be directly used in a select field
 * NOTE: the query *MUST* select 2 columns of data
 *
 * EX:
 * $accountTypes = fetchArrayForSelectField('select account_id, description from accountcodes');
 * $theForm->addSelect('ACCTTYPE', 'Account Type', $accountTypes, true);
 *
 * Also, with oracle, we had issues doing this:
 * select somefield, 1 from table
 * we had to change this to
 * select somefield, 1 as dummy from table
 * to get it to work.
 */
function fetchArrayForSelectField($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $array = $dbconn->GetAssoc($sql);
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $array;
}

/**
 * Build an array from a select statement that queries just one value
 * NOTE: the query *SHOULD* select only one field, but if it selects more, we will use only the first field
 *
 * EX:
 * $names = fetchArray("select firstname from staff");
 *
 * If there are 3 records in the table (Bill, Bill, and Sue), this will build an array like this for example
 * Array
 * (
 *   [0] => 'Bill'
 *   [1] => 'Bill'
 *   [2] => 'Sue'
 * )
 *
 */
function fetchArray($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->Execute($sql);
  checkError($dbconn);
  $items = $rs->GetRows();
  $toReturn = array();
  foreach ($items as $key=>$value) {
    $myVal = reset($value);
    $toReturn[] = $myVal;
  }
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $toReturn;
}

/**
 * Build an array of unique values from a select statement that queries just one value
 * NOTE: the query *SHOULD* select only one field, but if it selects more, we will use only the first field
 *
 * EX:
 * $names = fetchArrayUnique("select firstname from staff");
 *
 * If there are 3 records in the table (Bill, Bill, and Sue), this will build an array like this for example
 * Array
 * (
 *   [0] => 'Bill'
 *   [1] => 'Sue'
 * )
 *
 */
function fetchArrayUnique($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->Execute($sql);
  checkError($dbconn);
  $items = $rs->GetRows();
  $toReturn = array();
  foreach ($items as $key=>$value) {
    $myVal = reset($value);
    $toReturn[$myVal] = $myVal;
  }
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return array_values($toReturn);
}

/**
 * Build an array of unique values from a select statement that queries just one value
 * NOTE: the query *SHOULD* select only one field, but if it selects more, we will use only the first field
 *
 * EX:
 * $names = fetchArrayUnique("select firstname from staff");
 *
 * If there are 3 records in the table (Bill, Bill, and Sue), this will build an array like this for example
 * Array
 * (
 *   ['Bill'] => 'Bill'
 *   ['Sue'] => 'Sue'
 * )
 *
 */
function fetchArrayKeys($sql, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  if (DB_DEBUG_ON) {
    $start = microtime_float();
  }
  $rs = $dbconn->Execute($sql);
  checkError($dbconn);
  $items = $rs->GetRows();
  $toReturn = array();
  foreach ($items as $key=>$value) {
    $myVal = reset($value);
    $toReturn[$myVal] = $myVal;
  }
  checkError($dbconn);
  if (DB_DEBUG_ON) {
    reportTiming($start);
  }
  return $toReturn;
}



/**
 * Produce a string containing all the editable field names
 * in the form, separated by commas.  Used in forming
 * insert statements.
 *
 * EX:
 *
 * $theForm->addElement('NAME', 'Enter your name', 20, 20, true);
 * $theForm->addElement('ADDRESS', 'Enter your address', 40, 50, true);
 *
 * echo getFieldNames();
 *
 * produces:
 * (NAME, ADDRESS)
 *
 */
function getFieldNames(&$form) {
  $toReturn = ' (';
  $elementsToUse = $form->getEditableElements();
  foreach ($elementsToUse as $element) {
    $toReturn .= $element->name.', ';
  }
  return substr($toReturn ?? '', 0, -2).') ';
}

/**
 * Produce a string containg the field names, followed by their
 * values, useful for an insert statement.
 *
 * EX:
 *
 * $theForm->addElement('NAME', 'Enter your name', 20, 20, true);
 * $theForm->addElement('ADDRESS', 'Enter your address', 40, 50, true);
 *
 * after the user has submitted data for this form...
 *
 * echo getFieldsForInsert($theForm);
 *
 * produces:
 * (NAME, ADDRESS) values ('Tim Smith', '123 Main Street')
 *
 */
function getFieldsForInsert(&$form) {
  $toReturn = getFieldNames($form).' values (';
  $elementsToUse = $form->getEditableElements();
  foreach ($elementsToUse as $element) {
    $toReturn .= dbQuoteString($element->getValueForDb()).', ';
  }
  return substr($toReturn ?? '', 0, -2).') ';
}

function getFieldsForInsertWithNull(&$form) {
  $toReturn = getFieldNames($form).' values (';
  $elementsToUse = $form->getEditableElements();
  foreach ($elementsToUse as $element) {
    $toReturn .= dbQuoteStringWithNull($element->getValueForDb()).', ';
  }
  return substr($toReturn ?? '', 0, -2).') ';
}

function getFieldsForInsertFromArray(&$array) {
  $toReturn = ' ('.implode(', ', array_keys($array)).') values ';
  $arrayValues = array_values($array);
  dbQuoteStrings($arrayValues);
  $toReturn .= '('.implode(', ', $arrayValues).') ';
  return $toReturn;
}

function getFieldsForInsertWithNullFromArray(&$array) {
  $toReturn = ' ('.implode(', ', array_keys($array)).') values ';
  $arrayValues = array_values($array);
  dbQuoteStringsWithNull($arrayValues);
  $toReturn .= '('.implode(', ', $arrayValues).') ';
  return $toReturn;
}

function getFieldsForUpdateFromArray(&$array) {
  $toReturn = ' ';
  foreach ($array as $fieldname => $value) {
    $toReturn .= $fieldname.' = '.dbQuoteString($value).', ';
  }
  return substr($toReturn ?? '', 0, -2).' ';
}

function getFieldsForUpdateWithNullFromArray(&$array) {
  $toReturn = ' ';
  foreach ($array as $fieldname => $value) {
    $toReturn .= $fieldname.' = '.dbQuoteStringWithNull($value).', ';
  }
  return substr($toReturn ?? '', 0, -2).' ';
}

/**
 * Produce a string containing the field names and their values,
 * joined with the equal sign, suitable for an update statement
 *
 * EX:
 *
 * $theForm->addElement('NAME', 'Enter your name', 20, 20, true);
 * $theForm->addElement('ADDRESS', 'Enter your address', 40, 50, true);
 *
 * after the user has submitted data for this form...
 *
 * echo getFieldsForUpdate($theForm);
 *
 * produces:
 * NAME = 'Tim Smith', ADDRESS = '123 Main Street'
 *
 */
function getFieldsForUpdate(&$form) {
  $toReturn = ' ';
  $elementsToUse = $form->getEditableElements();
  foreach ($elementsToUse as $element) {
    $toReturn .= $element->name.' = '.dbQuoteString($element->getValueForDb()).', ';
  }
  return substr($toReturn ?? '', 0, -2).' ';
}

function getFieldsForUpdateWithNull(&$form) {
  $toReturn = ' ';
  $elementsToUse = $form->getEditableElements();
  foreach ($elementsToUse as $element) {
    $toReturn .= $element->name.' = '.dbQuoteStringWithNull($element->getValueForDb()).', ';
  }
  return substr($toReturn ?? '', 0, -2).' ';
}

function getNonEmptyFieldsForUpdate(&$form) {
  $toReturn = ' ';
  $elementsToUse = $form->getEditableElements();
  foreach ($elementsToUse as $element) {
    $value = $element->getValueForDb();
    if (isReallySet($value)) {
      $toReturn .= $element->name.' = '.dbQuoteString($value).', ';
    }
  }
  return substr($toReturn ?? '', 0, -2).' ';
}

/**
 * Gets the editable elements in the provided form
 * that are all prefixed by the specified tablename
 *
 * NOTE: the names of the elements returned will be
 * changed to _not_ include the prefix.
 *
 * ex: if you have a field of TABLE1-FIRST_NAME
 * and you call this method looking for elements
 * by table TABLE1, the resulting element returned
 * will have a field named FIRST_NAME
 */
function getEditableElementsByTable(&$form, $tableName) {
  $elementsToUse = $form->getEditableElements();
  $editableElements = array();
  foreach ($elementsToUse as $element) {
    $toSearchFor = $tableName.'-';
    if (strpos($element->name, $toSearchFor) === 0) {
      $editableElements[] = $element;
    }
  }
  return $editableElements;
}

/**
 * Similar to getFieldsForInsert, however this version
 * only returns data for fields whose names are prefixed
 * by the specified $tableName (using a hyphen)
 */
function getFieldsForInsertByTable(&$form, $tableName) {
  $toReturn = ' (';
  $elementsToUse = getEditableElementsByTable($form, $tableName);
  $names = '';
  $values = '';
  foreach ($elementsToUse as $element) {
    $names .= str_replace($tableName.'-', '', $element->name).', ';
    $values .= dbQuoteString($element->getValueForDb()).', ';
  }
  return '('.substr($names ?? '', 0, -2).') values ('.substr($values, 0, -2).') ';
}

/**
 * Similar to getFieldsForUpdate, however this version
 * only returns data for fields whose names are prefixed
 * by the specified $tableName (using a hyphen)
 */
function getFieldsForUpdateByTable(&$form, $tableName) {
  $toReturn = ' ';
  $elementsToUse = getEditableElementsByTable($form, $tableName);
  foreach ($elementsToUse as $element) {
    $toReturn .= str_replace($tableName.'-', '', $element->name).' = '.dbQuoteString($element->getValueForDb()).', ';
  }
  return substr($toReturn ?? '', 0, -2).' ';
}

function buildUpdateFromRequest($tableName, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  $columns = array_change_key_case($dbconn->MetaColumns($tableName), CASE_LOWER);
  $columnStrings = array();
  // convert both metadata of col names and the REQUEST 
  // keys to lowercase for ease of comparisions.
  $_REQUEST = array_change_key_case($_REQUEST, CASE_LOWER);
  foreach ($columns as $column) {
    $colName = $column->name;
    if (isset($_REQUEST[$colName])) {
      $columnStrings[] = $column->name.' = '.dbQuoteString($_REQUEST[$colName], $dbname);
    }
  }
  $keys = $dbconn->MetaPrimaryKeys($tableName);
  if (null == $keys) {
    logError("Unable to build an update statement for table: $tableName because it has no primary key defined in the database");
  }
  array_change_key_case($keys, CASE_LOWER);
  $keyStrings = array();
  foreach ($keys as $key) {
    $lowerKey = strtolower($key);
    $keyStrings[] = $lowerKey.' = '.dbQuoteString($_REQUEST[$lowerKey], $dbname);
  }
  return "update $tableName set ".implode(', ', $columnStrings).' where '.implode(' and ', $keyStrings);
}

function buildInsertFromRequest($tableName, $dbname = NULL) {
  // get columns
  // subtract key column
  // build insert statement with what's left
  // should do sequence via trigger for oracle db, to simulate an autoincrement key
  $dbconn = getNamedConnection($dbname);
  $columns = array_change_key_case($dbconn->MetaColumns($tableName));
  // convert both metadata of col names and the REQUEST 
  // keys to lowercase for ease of comparisions.
  $_REQUEST = array_change_key_case($_REQUEST, CASE_LOWER);
  $columnNames = array();
  $columnValues = array();
  foreach ($columns as $column) {
    $colName = $column->name;
    if (isset($_REQUEST[strtoupper($colName)]) || isset($_REQUEST[strtolower($colName)])) {
      $columnNames[] = $column->name;
    }
  }

  foreach ($columnNames as $columnName) {
    $lowerColName = strtolower($columnName);
    $columnValues[] = dbQuoteString($_REQUEST[$lowerColName]);
  }

  return "insert into $tableName (".implode(', ', $columnNames).") values (".implode(', ', $columnValues).")";
}

function dbDate($format, $fieldName, $dbname = NULL) {
  $dbconn = getNamedConnection($dbname);
  $pos = strpos($fieldName, '.');
  if (0 < $pos) {
    $fieldNameWithoutDot = substr($fieldName ?? '', $pos + 1);
  }
  else {
    $fieldNameWithoutDot = $fieldName;
  }
  return $dbconn->SQLDate($format, $fieldName).' as '.$fieldNameWithoutDot;
}

?>
