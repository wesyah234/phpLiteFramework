<?php
/**
 * Default configuration options that may be overriden in the project's
 * index.php file (be sure to change from setDefault to define
 * if copying from here)
 *
 * define('CONSTANT_NAME', value);
 *
 */

//
// set to true to use clean urls without needing any mod_rewrite functionality
// if false, makeUrl ('mymodule', 'myfunction', array('parm1'=>'value1', 'parm2'=>'val2'))
// will create:
//  http://mysite.com/?module=mymodule&func=myfunction&parm1=value1&parm2=val2
// if true,  makeUrl ('mymodule', 'myfunction', array('parm1'=>'value1', 'parm2'=>'val2'))
// will create:
//  http://mysite.com/?mymodule/myfunction/parm1/value1/parm2/val2
//
//
setDefault('CLEANURLS', false);

// set to true to allow the framework to be used in scripts other than the main controller (index.php) and therefore be called from
// a web environment.  Generally left at the default value of false.
setDefault('EMBED_FRAMEWORK_IN_ANOTHER_WEBAPP', false);

// javascript library include settings
// NOTE: In IE 8.0, we noticed that there is some conflict between prototype/scriptaculous
//       and jquery, so we added these flags to allow us to turn off the include
//       of prototype/scriptaculous.  A project can set these as needed in their
//       index.php if they want one over the other.  Also note, some of the 
//       helper javascript functions in plf.js use jquery.  Further, you can include
//       a js framework yourself in your own default.html template for the project
//       and not bother with the ones included here by the framework.
// should we include the prototype and scriptaculous frameworks on every page?
setDefault('USE_PROTOTYPE_SCRIPTACULOUS', false);
// should we include the jquery framework on every page?
setDefault('USE_JQUERY', true);

setDefault('STATUS_MESSAGE_DIV_CLASS', 'plf_status_red');

// Database settings
// do you want to see the sql statements being executed?
setDefault('DB_DEBUG_ON', false);
// set to true to be able to use dbQuoteString function without having a database set up
setDefault('DEMO_MODE', false);
// specify whether to use PConnect or Connect when connecting to the database
// PConnect will generally attempt to share/reuse database connections for better performance
// however, in 2016, with postgres, I was getting the error pg_query(): Cannot set connection to blocking mode
// sporadically, and it was suggested somewhere to *not* use peristent connections. so my recommendation is to set this to
// false when working with postgresql
setDefault('USE_PERSISTENT_DB_CONNECT', true);
// orig design was to specify a SERVICE NAME for oracle connections
// now if this flag is set to true, the db name can be a SID and it will connect
setDefault('DB_USE_SID', false);

// per http://phplens.com/lens/adodb/docs-adodb.htm#adodb_countrecs
// ADODB_COUNTRECS is true by default, but one may switch it to false for performance
// and to avoid a memory crash too with large datasets
// HOWEVER, WARNING: setting this to false might also introduce Oracle errors/warnings
// that were previously not appearing.
// ORA-24347: Warning of a NULL column in an aggregate function
// if you need to set this to false, you will also need to fix your queries so that they don't
// give the NULL column error
// see this thread: 
// http://phplens.com/lens/lensforum/msgs.php?id=19126
setDefault('DB_ADODB_COUNTRECS', true);

// this framework uses adodb, a db abstraction library for PHP, which allows one
// to set an oracle specific session setting for the oracle database connections
// The oracle default will return dates like YYYY-MM-DD, but the framework uses
// a date popup that likes dates to be MM/DD/YYYY, so we will set all connections
// to MM/DD/YYYY by default with this variable.  If you want something else for some reason
// You may change it by 'defining' this variable to something else.
// Regardless of this setting, you may retrieve dates and times in a different format by
// using the to_char function in your select statements.
setDefault('DB_ORACLE_NLS_DATE_FORMAT', 'MM/DD/YYYY');


// the ones below have no logical defaults, so just copy
// and put them in your index.php
//  define('DBHOSTNAME', 'somehostname');
//  define('DBUSERNAME', 'someusername');
//  define('DBPASSWORD', 'somepassword');
//  define('DBNAME', 'somedbservicename.something.else');  
//  define('DBCHARSET', 'utf8');  

// General Framework Settings

// the directory holding the Zend Framework (minimal).. ie. just the libraries
// projects can override if they want to stick to an older version when we start
// including a newer version in PLF
setDefault('ZEND_FRAMEWORK_VERSION_DIR', '1.10.1');


// If a file with this name exists in the root of the web application, its contents
// will be displayed instead of the normal web application
setDefault('SITE_DOWN_FILENAME', 'sitedown.txt');


setDefault('SESSION_EXPIRE_MINUTES', 30);

// this specifies the value to store on the db when user checks 
// a form checkbox, should probably use a number for database
// efficiency, if possible, otherwise set it to what your 
// existing database uses if you're inheriting a system
setDefault('CHECKBOX_CHECKED', 'T');
// this specifies the value to store on the db when user leave
// a form chekbox unchecked
setDefault('CHECKBOX_UNCHECKED', 'F');

// this specifies the value to display when calling displayCheckbox
// for a database value that was stored using a form checkbox field
setDefault('CHECKBOX_CHECKED_DISPLAY', 'T');
// see CHECKBOX_CHECKED_DISPLAY above, this is what to show if 
// checkbox wasn't checked
setDefault('CHECKBOX_UNCHECKED_DISPLAY', 'F');

// sets the module and function to call if the user loads the homepage, ie
// goes to the website without a "module= function=" type url
// or with any random url that doesn't map to a function that returns data
setDefault('DEFAULTMODULE', 'mainPage');
setDefault('DEFAULTFUNC', 'contents');
// the function to load if the user's session has expired
// and we attempt to grab a session variable
setDefault('DEFAULT_SESSION_EXPIRED_FUNC', 'sessionExpiredMessage');

// the name of the website for the browser title bar
setDefault('WEBSITENAME', 'Website Name Override This Please');

// indicates if errors with stack trace should be echoed to the browser
// in addition to being logged
// NOTE: we don't allow error logging to be suppressed from the log file
// like we allow the other severities to be suppressed.  (see LOGWARNINGS,
// LOGNOTICES, and LOGEVERYTHINGELSE)
setDefault('SHOWERRORSTOUSER', true);

// the following 2 settings are independent:
// indicates if warnings should be written to the webserver log file
setDefault('LOGWARNINGS', true);
// indicates if warnings should be echoed to the user
setDefault('SHOWWARNINGSTOUSER', true);

// the following 2 settings are independent:
// indicates if notices should be written to the webserver log file
setDefault('LOGNOTICES', true);
// indicates if notices should be echoed to the user
setDefault('SHOWNOTICESTOUSER', true);


// the following 2 settings are independent:
// indicates if any other errors, notices, warnings etc
// not specifically raised by the framework (ie. those raised
// by third party libs, or the PHP language itself) should
// be logged to the webserver log file
setDefault('LOGEVERYTHINGELSE', true);
// indicates if any other errors, notices, warnings etc
// not specifically raised by the framework (ie. those raised
// by third party libs, or the PHP language itself) should
// be echoed to the user
setDefault('SHOWEVERYTHINGELSETOUSER', true);
// log everything else to the log file?
setDefault('LOGEVERYTHINGELSE', true);

setDefault('PHPDATEFORMAT', 'm/d/Y');
setDefault('PHPDATETIMEFORMAT', 'm/d/Y h:i:s A');

// do you want the user forced into https mode?
setDefault('FORCESSL', false);

// the error message to show whenever the code calls logError
// and SHOWERRORSTOUSER is false
// the actual error message will show in the log file, but the user
// won't see it
setDefault('FRIENDLY_ERROR_MSG_FOR_USER', 'We have encountered an internal error, we will resolve it as soon as possible, please check back soon');
// do you want to email the administrator whenever logError is called
// (great for production)
setDefault('EMAIL_ADMIN_ON_ERROR', false);
// address to mail the error email
setDefault('ADMIN_EMAIL_ADDR', 'adminAddress@example.com');
// what to prepend to the error email subject line (great for people with
// lots of email messages)
setDefault('ADMIN_EMAIL_SUBJECT_PREPEND', '');

// ---------------------------------------------------------------------------------
// the following parameters may be changed to test out emailing without
// actually sending email to the intended recipients.
// the subject line of the email will contain the intended recipient
// ---------------------------------------------------------------------------------

// set to false if you want to send mail to the TEMP_EMAIL_ADDRESS
// instead of the actual recipient
setDefault('REALLYSENDMAIL', true);

// only used if REALLYSENDMAIL above is false
// this is the recipient email address -
// it can be multiple addresses separated by commas
//
// the actual intended recipient's address will be put into the subject
// of the email message so you know who it's intended to go to once
// the REALLYSENDMAIL flag is set to true.
setDefault('TEMP_EMAIL_ADDRESS', 'address@example.com');

setDefault('MAIL_SMTPSERVER', '');
setDefault('MAIL_SMTPUSERNAME', '');
setDefault('MAIL_SMTPPASSWORD', '');
// use 465 for secure, plus also set MAIL_SMTPSECURE to 1
setDefault('MAIL_SMTPPORT', '25');
setDefault('MAIL_SMTPSECURE', '0');
?>
