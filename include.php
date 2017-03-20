<?php
$config_file = 'data/config';

$pages = array(
	'now'=>"What's On Now?",
	'telly'=>'Channels',
	'fav'=>'Favourite Channels',
	'timers'=>'Timers',
	'recordings'=>'Recordings',
	'links'=>'Series Links',
	'config'=>'Configuration');

if (file_exists($config_file)) {
  $conf = file_get_contents($config_file);
  $settings = json_decode($conf, true);
  $user = $settings['USER'];
  $pass = $settings['PASS'];
  $ip = $settings['IP'];
  $urlp = "http://" . $user . ":" . $pass . "@" . $ip;
  $profile = $settings['PROFILE'];
  $config_uuid = $settings['UUID'];
  $epg_start = $settings['EPGSTART'];
}
else if ((strpos($_SERVER['PHP_SELF'], 'config.php') === false) &&
	(strpos($_SERVER['PHP_SELF'], 'configure.php') === false)) {
  header('Location: config.php');
  die;
}

function get_epg($channel) {
  global $urlp;
  $prog = urlencode($channel);
  $url = "$urlp/api/epg/events/grid?limit=999&channel=$prog";
  $json = curl_file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function search_epg($channel,$title) {
  global $urlp;
  $prog = urlencode($channel);
  $ttl = urlencode(preg_quote($title)); 
  $url = "$urlp/api/epg/events/grid?limit=999&title=$ttl";
  if ($channel != "") $url .= "&channel=$prog";
  $json = curl_file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_timers() {
  global $urlp;
  $url = "$urlp/api/dvr/entry/grid_upcoming";
  $json = curl_file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  usort($ret, "sort_timers");
  return $ret;
}

function get_recordings() {
  global $urlp;
  $url = "$urlp/api/dvr/entry/grid_finished";
  $json = curl_file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  usort($ret, "sort_recordings");
  return $ret;
}

function get_channels() {
  global $urlp;
  $url = "$urlp/api/channel/list";
  $json = curl_file_get_contents($url);
  $c = json_decode($json, true);
  $ret = &$c["entries"];
  usort($ret, "sort_channels");
  return $ret;
}

function get_links() {
  global $urlp;
  $url = "$urlp/api/dvr/autorec/grid";
  $json = curl_file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}

function get_profiles() {
  global $urlp;
  $url = "$urlp/api/dvr/config/grid";
  $json = curl_file_get_contents($url);
  $j = json_decode($json, true);
  $ret = &$j["entries"];
  return $ret;
}


function sort_channels($a, $b) {
  return strcasecmp($a["val"], $b["val"]);
}

function sort_timers($a, $b) {
  return ($a["start"] - $b["start"]);
}

function sort_recordings($a, $b) {
  return ($a["start"] - $b["start"]);
}

function curl_file_get_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else {
#        echo "Failed to get contents for $URL";
        return FALSE;
    }
}

function check_timer($timers, $t) {
  if (count($timers) < 2) return true;
  $tstart = $t["start"];
  $tstop = $t["stop"];
  $tuuid = $t["uuid"];
  foreach ($timers as $m) {
    if (!$m["enabled"]) continue;
    if ($m["uuid"] == $tuuid) continue;
    if(($tstart >= $m["start"] && $tstart < $m["stop"])
	||($m["start"] >= $tstart && $m["start"] < $tstop)) {
	  return false;
    }
  }
  return true;
} 
?>
