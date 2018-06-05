<?php
$config_file = 'data/config';

$pages = array(
	'now'=>"What's On Now?",
	'telly'=>'Channels',
	'fav'=>'Favourite Channels',
	'timers'=>'Timers',
	'recordings'=>'Recordings',
	'links'=>'Series Links',
	'status'=>'Status',
	'config'=>'Configuration');

$orders = array(
	0 => "Date",
	1 => "Title");

$types = [1=>"SDTV",2=>"Radio",17=>"HDTV",22=>"SDTV",23=>"SDTV",24=>"SDTV",25=>"HDTV",
	 26=>"HDTV",27=>"HDTV",28=>"HDTV",29=>"HDTV",30=>"HDTV",31=>"UHDTV"];

if (file_exists($config_file)) {
  $conf = file_get_contents($config_file);
  $settings = json_decode($conf, true);
  $user = $settings['USER'];
  $pass = $settings['PASS'];
  $ip = $settings['IP'];
  if ($user == '') {
    $urlp = "http://$ip";
  }
  else if ($pass == '') {
    $urlp = "http://$user@$ip";
  }
  else $urlp = "http://$user:$pass@$ip";
  $profile = $settings['PROFILE'];
  $config_uuid = $settings['UUID'];
  $epg_start = $settings['EPGSTART'];
  $sort = $settings['SORT'];
  if (!isset($settings['SUMM'])) $settings['SUMM'] = 'summary';
}
else if ((strpos($_SERVER['PHP_SELF'], 'config.php') === false) &&
	(strpos($_SERVER['PHP_SELF'], 'configure.php') === false)) {
  header('Location: config.php');
  die;
}

function get_epg($channel) {
  global $urlp;
  $prog = urlencode($channel);
  $url = "$urlp/api/epg/events/grid?limit=9999&channel=$prog";
  $json = preg_replace('/[^(\x20-\x7F)]/',"",file_get_contents($url));
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_epg_now($channel) {
  global $urlp;
  $prog = urlencode($channel);
  $url = "$urlp/api/epg/events/grid?channel=$prog&mode=now";
  $json = preg_replace('/[^(\x20-\x7F)]/',"",file_get_contents($url));
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function search_epg($channel,$title) {
  global $urlp;
  $prog = urlencode($channel);
  $ttl = urlencode(preg_quote($title)); 
  $url = "$urlp/api/epg/events/grid?limit=9999&title=$ttl";
  if ($channel != "") $url .= "&channel=$prog";
  $json = preg_replace('/[^(\x20-\x7F)]/',"",file_get_contents($url));
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_timers() {
  global $urlp;
  $url = "$urlp/api/dvr/entry/grid_upcoming?sort=start";
  $json = preg_replace('/[^(\x20-\x7F)]/',"",file_get_contents($url));
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_recordings($s) {
  global $urlp;
  $url = "$urlp/api/dvr/entry/grid?limit=99999";
  $json = preg_replace('/[^(\x20-\x7F)]/',"",file_get_contents($url));
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  if($s == 0) usort($ret, "sort_recordings");
  else usort($ret, "sort_recordings_title");
  return $ret;
}

function get_channels() {
  global $urlp;
  $url = "$urlp/api/channel/grid?limit=9999";
  $json = file_get_contents($url);
  $c = json_decode($json, true);
  $ret = &$c["entries"];
  usort($ret, "sort_channels");
  return $ret;
}

function get_links() {
  global $urlp;
  $url = "$urlp/api/dvr/autorec/grid";
  $json = file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_profiles() {
  global $urlp;
  $url = "$urlp/api/dvr/config/grid";
  $json = file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_services() {
  global $urlp;
  $url = "$urlp/api/mpegts/service/grid?hidemode=all&limit=9999";
  $json = file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function sort_channels($a, $b) {
  return strcasecmp($a["name"], $b["name"]);
}

function sort_recordings($a, $b) {
  return ($a["start"] - $b["start"]);
}

function sort_recordings_title($a, $b) {
  $x = $a["disp_title"];
  $y = $b["disp_title"];
  if(strncmp($x, 'New: ', 5) == 0) {
    $x = substr($x, 5);
    if(substr($x, -3) == '...') {
      $x = substr($x, 0, -3);
    }
  }
  if(strncmp($y, 'New: ', 5) == 0) {
    $y = substr($y, 5);
    if(substr($y, -3) == '...') {
      $y = substr($y, 0, -3);
    }
  }
  $n = min(strlen($x), strlen($y));
  $ret = strncmp($x, $y, $n);
  if($ret == 0) return ($a["start"] - $b["start"]);
  return $ret;
}
?>
