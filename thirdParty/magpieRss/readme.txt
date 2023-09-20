This is the magpie rss feed utility from:

http://magpierss.sourceforge.net/

There is a framework function named fetchRss($url) that wraps the main fetch_rss
method provided by magpie.  This wrapper was created so that the include of the
magpie library can be transparent to the user, and only performed if rss fetching
is being used in the current script.