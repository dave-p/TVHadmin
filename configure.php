<?php
	include_once "./include.php";
	$vars = $_POST;
	$enc = json_encode($vars);
	file_put_contents($config_file, $enc);
	header('Location: TVHadmin.php?screen=config'); 
?>
