<?php
	include_once "./include.php";
	$uuid = $_GET["uuid"];
	$url = "$urlp/api/idnode/delete?uuid=$uuid";
	curl_file_get_contents($url);
	header('Location: links.php');
?>
