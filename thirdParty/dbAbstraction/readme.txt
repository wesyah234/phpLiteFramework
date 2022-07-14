Update 5/12/2016:
brought in adodb 5.20.4 into new directory adodb-5.20.4
this version is nearly 7 years newer than the last.  it's about time we include it
-----------------

The adodb5 directory is the latest version (5.10) as of 11/2009 and the define 
in plf.inc.php references this directory:

define('ADODB_DIR', dirname(__FILE__).'/thirdParty/dbAbstraction/adodb5'); 

however, we're including the old 4.68 version in case anyone has problems with the newer version.
just change the define in plf.inc.php to point at the adodb directory.

general info:

http://adodb.sourceforge.net/

various methods in phpLiteFramework/util/dbFunctions.php provide thin wrappers around
the methods in the adodb library.  Facilitate dealing with a default database connection
across the whole project, and also deal with checking for db errors.