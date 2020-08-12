# TVHadmin
A PHP-powered web front-end for TVHeadend, inspired by VDRadmin.

This project provides a simple web-based front-end for TVHeadend. It is inspired by VDRadmin-am, written by Andreas Mair (http://andreas.vdr-developer.org/vdradmin-am/index.html). It was written for UK Freeview and has been tested with UK Freesat but should also work with any TV provider supported by TVHeadend.

Sample screenshots can be found [here](/screenshots/now.png) [here](/screenshots/timeline.png) and [here](/screenshots/favourite.png).

### Requirements
- A recent version of TVHeadend (at least v4.2 - the latest development version preferred).
- A web server to host TVHadmin, with PHP available.
- PHP must have the JSON and XML extensions available. They are built-in by default in recent versions, but some distributions package them separately.
- The PHP.INI setting "allow_url_fopen" must be ON.

Note that the web server is responsible for security! For use across the internet you should configure authentication on the web server and preferably use SSL. If the web server and TVH server are not on the same local network, bear in mind that the TVH username and password are stored in clear text on the web server and are sent in clear text from web server to TVH.

### Installation
- Copy the files to your web server's HTTPROOT or a subdirectory
- The `data` subdirectory is used to store configuration settings, and it must be writeable by the web server user (usually 'http' or 'apache'). If you need to change this location, edit the first line of include.php to match the location and name of the config file (relative to include.php)
- Check if `open_basedir` has been set in your PHP.INI file. If so, ensure that the locations of the TVHadmin files and the config file are under the open_basedir directory
- Check that your TVHeadend server is configured to use 'plain' or 'plain and digest' authentication (Configuration->General->Base->HTTP Server Settings). TVHadmin won't currently work with 'digest' authentication.
- Browse to http://your.web.server/path/TVHadmin.php. Enter the username and password of a TVHeadend admin user. In the "IP address:port" box enter the details of your TVHeadend server; if you have started TVHeadend with the "--http_root" option then add the directory after the port number. Click the 'save' button.
- If connection to TVHeadend succeeds you will see further configuration options. Make any changes then click 'save' again.
- TVHadmin should now be working.
