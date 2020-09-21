<?php
$config_file = 'data/config';

$pages = array(
	'now'=>"What's On Now?",
	'timeline'=>"Timeline",
	'telly'=>'Channels',
	'fav'=>'Favourite Channels',
	'timers'=>'Timers',
	'recordings'=>'Recordings',
	'links'=>'Series Links',
	'status'=>'Status',
	'config'=>'Configuration');

$orders = array(
	0 => "Date Fwd",
	1 => "Date Rev",
	2 => "Title");

$c_orders = array(
	0 => "Name",
	1 => "LCN");

if (file_exists($config_file)) {
  $conf = file_get_contents($config_file);
  $settings = json_decode($conf, true);
  $user = $settings['USER'];
  $pass = $settings['PASS'];
  $ip = $settings['IP'];
  $urlp = 'http://' . $ip;
  if ($user != '') {
    if ($pass == '') $auth = base64_encode($user);
    else $auth = base64_encode("$user:$pass");
    stream_context_set_default(
      [
	'http' => [
	  'method' => 'GET',
	  'header' => 'Authorization: Basic ' . $auth
	]
      ]
    );
  }
  $config_uuid = $settings['UUID'];
  $epg_start = $settings['EPGSTART'];
}
else if ((strpos($_SERVER['PHP_SELF'], 'config.php') === false) &&
	(strpos($_SERVER['PHP_SELF'], 'configure.php') === false)) {
  header('Location: config.php');
  die;
}

function get_epg_now($channel) {
  global $urlp;
  $prog = urlencode($channel);
  $url = "$urlp/api/epg/events/grid?channel=$prog&mode=now";
  $json = file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

#return events ending after the 'from' time and beginning before the 'to' time.

function get_epg($channel, $from, $to) {
  global $urlp, $auth;
  if ($to != 0) {
	$data = array("channel"=>"$channel","filter"=>"[{\"field\":\"stop\",\"type\":\"numeric\",\"value\":\"{$from}\",\"comparison\":\"gt\"}, {\"field\":\"start\",\"type\":\"numeric\",\"value\":\"{$to}\",\"comparison\":\"lt\"}]");
  }
  else {
	$data = array("channel"=>"$channel","limit"=>9999);
  }
  $query = http_build_query($data);
  $ctx = stream_context_create(array('http' => array(
	'method' => 'POST',
	'header'  => "Content-type: application/x-www-form-urlencoded\r\n" .
			'Authorization: Basic ' . $auth,
	'content' => $query )));
  $url = "$urlp/api/epg/events/grid";
  $json = file_get_contents($url, false, $ctx);
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
  $json = file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_timers() {
  global $urlp;
  $url = "$urlp/api/dvr/entry/grid_upcoming?sort=start";
  $json = file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_recordings($s) {
  global $urlp;
  $url = "$urlp/api/dvr/entry/grid?limit=99999";
  $json = file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  switch($s) {
    case 0:
      usort($ret, "sort_recordings");
      break;
    case 1:
      usort($ret, "sort_recordings_desc");
      break;
    case 2:
      usort($ret, "sort_recordings_title");
  }
  return $ret;
}

function get_channels() {
  global $urlp, $settings;
  $url = "$urlp/api/channel/grid?limit=9999";
  $json = file_get_contents($url);
  $c = json_decode($json, true);
  $ret = &$c["entries"];
  if (isset($settings["CSORT"]) && ($settings["CSORT"] == 1)) {
    usort($ret, "sort_channels_lcn");
  }
  else usort($ret, "sort_channels");
  return $ret;
}

function get_channeltags() {
  global $urlp;
  $url = "$urlp/api/channeltag/list";
  $json = file_get_contents($url);
  $c = json_decode($json, true);
  $ret = array('All' => 'All');
  foreach ($c["entries"] as $t) {
    $ret[$t["key"]] = $t["val"];
  }
  return $ret;
}

function get_links() {
  global $urlp;
  $url = "$urlp/api/dvr/autorec/grid";
  $json = file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  usort($ret, "sort_links");
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

function sort_channels($a, $b) {
  return strcasecmp($a["name"], $b["name"]);
}

function sort_channels_lcn($a, $b) {
  $ret = $a["number"] - $b["number"];
  if ($ret == 0) return strcasecmp($a["name"], $b["name"]);
  else return $ret;
}

function sort_recordings($a, $b) {
  return ($a["start"] - $b["start"]);
}

function sort_recordings_desc($a, $b) {
  return ($b["start"] - $a["start"]);
}

function sort_recordings_title($a, $b) {
  $x = $a["disp_title"];
  $y = $b["disp_title"];
  if(strncmp($x, 'New:', 4) == 0) {
    if($x[4] == ' ') $x = substr($x, 5);
    else $x = substr($x, 4);
    if(substr($x, -3) == '...') {
      $x = substr($x, 0, -3);
    }
  }
  if(strncmp($y, 'New:', 4) == 0) {
    if($y[4] == ' ') $y = substr($y, 5);
    else $y = substr($y, 4);
    if(substr($y, -3) == '...') {
      $y = substr($y, 0, -3);
    }
  }
  $n = min(strlen($x), strlen($y));
  $ret = strncasecmp($x, $y, $n);
  if($ret == 0) return ($a["start"] - $b["start"]);
  return $ret;
}

function sort_links($a, $b) {
  $x = $a["title"];
  $y = $b["title"];
  if(strncmp($x, 'New:', 4) == 0) {
    if($x[4] == ' ') $x = substr($x, 5);
    else $x = substr($x, 4);
    if(substr($x, -3) == '...') {
      $x = substr($x, 0, -3);
    }
  }
  if(strncmp($y, 'New:', 4) == 0) {
    if($y[4] == ' ') $y = substr($y, 5);
    else $y = substr($y, 4);
    if(substr($y, -3) == '...') {
      $y = substr($y, 0, -3);
    }
  }
  $n = min(strlen($x), strlen($y));
  $ret = strncmp($x, $y, $n);
  return $ret;
}
?>
