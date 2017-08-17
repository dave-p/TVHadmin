<?php
	include_once "./include.php";
	$uuid = $_GET["uuid"];
	$url = "$urlp/api/dvr/entry/cancel?uuid=$uuid";
	file_get_contents($url);
	header('Location: timers.php');
?>
