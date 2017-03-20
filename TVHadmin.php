<?php
	include_once "./include.php";
	if (isset($_GET['screen'])) $page = $_GET['screen'] . '.php';
	else $page = './' . $settings['HOME'] . '.php';
	if (!empty($page) && file_exists($page)) include_once($page);
	else include_once "./config.php";
?>
