<?php
	include_once "./include.php";
	$evt = $_GET["eventId"];
	$id = $_GET['id'];
	$when = $_GET['when'];
	if (isset($_GET['prog'])) {
	  $prog = $_GET['prog'];
	}
	else {
	  $prog = '';
	}
	if ($_GET["series"] == 'Y') {
	  $url = "$urlp/api/dvr/autorec/create_by_series?event_id=$evt&config_uuid=$config_uuid";
	}
	else {
	  $url = "$urlp/api/dvr/entry/create_by_event?event_id=$evt&config_uuid=$config_uuid";
	}
	curl_file_get_contents($url);
	$from = $_GET['from'];
	if ($from == 1) {
	  header("Location: fav.php?when=$when#$id");
	}
	else {
	  header("Location: telly.php?when=$when&prog=$prog#$id");
	}
?>
