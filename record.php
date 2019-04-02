<?php
	include_once "./include.php";
	$evt = $_GET["eventId"];
	$id = $_GET['id'];
	if (isset($_GET['when'])) {
	  $when = $_GET['when'];
	}
	if (isset($_GET['prog'])) {
	  $prog = urlencode($_GET['prog']);
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
	file_get_contents($url);
	$from = $_GET['from'];
	switch ($from) {
	  case 1:
	    header("Location: fav.php?when=$when#$id");
	    break;
	  case 2:
	    header("Location: telly.php?when=$when&prog=$prog#$id");
	    break;
	  case 3:
	    header("Location: search.php?find=$id");
	    break;
	  default:
	    header("Location: TVHadmin.php");
	}
?>
