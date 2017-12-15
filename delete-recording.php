<?php
	include_once "./include.php";
	$uuid = $_GET["uuid"];
	$url = "$urlp/api/dvr/entry/remove?uuid=$uuid";
	file_get_contents($url);
	header('Location: recordings.php');
?>
