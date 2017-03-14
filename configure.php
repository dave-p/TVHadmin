<?php
	include_once "include.php";
	$enc = json_encode($_POST);
	file_put_contents($config_file, $enc);
	echo "<script>top.window.location='fav.html'</script>"; 
?>
