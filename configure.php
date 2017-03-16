<?php
	include_once "include.php";
	$enc = json_encode($_POST);
	file_put_contents($config_file, $enc);
	header('Location: fav.php'); 
?>
