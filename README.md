# TVHadmin
A PHP-powered web front-end for TVHeadend, inspired by VDRadmin.

This project provides a simple web-based front-end for TVHeadend. It is inspired by VDRadmin-am, written by Andreas Mair (http://andreas.vdr-developer.org/vdradmin-am/index.html).


### Requirements:
- A recent version of TVHeadend (at least v4.2 - the latest development version preferred).
- A web server to host TVHadmin, with PHP available.
- PHP must be able to read/write files in a suitable directory; see the 'open_basedir' directive in PHP.INI.
- The cURL PHP module must be installed.

Note that the web server is responsible for security! For use across the internet you should configure authentication on the web server and preferably use SSL.

### Installation
- Copy the files to your web server's HTTPROOT or a subdirectory
- Edit the first link of includes.php to match the location (relative to includes.php) where the configuration settings will be written
- Browse to http://your.server/path/TVHadmin.php. Fill in the details of your TVHeadend server and click the 'save' button.
- If connection to TVHeadend succeeds you will see further configuration options. Make any changes then click 'save' again.
- TVHadmin should now be working.
