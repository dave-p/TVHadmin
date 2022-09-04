# TVHadmin
A PHP-powered web front-end for TVHeadend, inspired by VDRadmin.

This project provides a simple web-based front-end for TVHeadend. It is inspired by VDRadmin-am, written by Andreas Mair (http://andreas.vdr-developer.org/vdradmin-am/index.html). It was written for UK Freeview and has been tested with UK Freesat but should also work with any TV provider supported by TVHeadend.

An alternative version written in Javascript which does not need a web server is at https://github.com/dave-p/TVHadmin-JS.

Sample screenshots can be found [here](/screenshots/).

### Requirements
- A recent version of TVHeadend (at least v4.2 - the latest development version preferred).
- A web server to host TVHadmin, with PHP available.
- PHP must have the JSON and XML extensions available. They are built-in by default in recent versions, but some distributions package them separately.
- The `date.timezone` directive in your PHP.INI file must be set to the timezone of your TVHeadend server.
- The PHP.INI setting "allow_url_fopen" must be ON.

Note that the web server is responsible for security! For use across the internet you should configure authentication on the web server and preferably use SSL. If the web server and TVH server are not on the same local network, bear in mind that the TVH username and password are stored in clear text on the web server and are sent in clear text from web server to TVH.

### Installation
- Copy the files to your web server's HTTPROOT or a subdirectory
- The `data` subdirectory is used to store configuration settings, and it must be writeable by the web server user (usually 'http' or 'apache'). If you need to change this location, edit the first line of include.php to match the location and name of the config file (relative to include.php)
- Check if `open_basedir` has been set in your PHP.INI file. If so, ensure that the locations of the TVHadmin files and the config file are under the open_basedir directory
- Check that your TVHeadend server is configured to use 'plain' or 'plain and digest' authentication (Configuration->General->Base->HTTP Server Settings). TVHadmin won't currently work with 'digest' authentication.
- If necessary create a TVHeadend user. The user must have the 'Web Interface' box ticked, and for full fuctionality should have all the 'Streaming' and 'Video Recorder' boxes ticked. If you are using Kodi with the 'TVHeadend HTSP' plugin, you should make use of the same user for both TVHadmin and Kodi.
- Browse to http://your.web.server/path/TVHadmin.php. Enter the username and password of the TVHeadend user. In the "IP address:port" box enter the details of your TVHeadend server; if you have started TVHeadend with the "--http_root" option then add the directory after the port number. Click the 'Connect' button.
- If connection to TVHeadend succeeds you will see further configuration options. Make any changes then click 'Save'.
- TVHadmin should now be working.

### Timer Clashes
TVHadmin can optionally detect and warn about timer clashes (where there is no free source to make a recording). Clash detection is set up using the Configuration screen.

Clash detection has not been fully tested with IPTV and other non-tuner sources.

#### Single Tuner
TVHadmin shows a timer clash by displaying a red 'tick' against any conflicting timers. Below the main display is shown any alternative broadcasts of the same programme. These alternatives are in turn checked to see if they clash with any other timers.

The alternatives display depends on the broadcasters providing 'series link' information; it has been tested on UK Freeview and Freesat.

A yellow 'tick' mark is shown if overlapping timers are from the same network and mux - tuners will normally allow multiple channels on the same mux to be recorded.

#### Multiple Tuners
TVHadmin checks the allocation of sources to timers using the same algorithm as TVHeadend. However in order for the check to work correctly it is important that each source for a channel should have a different priority set - if TVHeadend has two or more sources with the same priority to make a recording it will choose one at random, so the clash detection will not be accurate.

The priority for a recording source is the sum of the service priority and the tuner priority (network priority for IPTV). If not using IPTV the simplest approach is to set each TV tuner to a different priority and leave the service priorities as default. If TVHadmin detects that there are multiple 'best' sources for a recording with the same priority, the 'tick' mark against the recording will show grey.

