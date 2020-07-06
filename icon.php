<?php
	/* Original source http://wiki.openstreetmap.org/wiki/ProxySimplePHP
	*/

    if (preg_match("/imagecache\/\d+$/", $_GET["image"])) {
	$img = null;
	$ctx = stream_context_create(array(
	  'http'=>array(
		'timeout'=>3,
		'user_agent'=>"TVHadmin",
		'header'=>"Content-Type: image/png\r\n" .
			'Authorization: Basic ' . $_GET["auth"]
	  )));
	$img = file_get_contents($_GET["image"], false, $ctx);
	if ($img) {
	    header ('Content-Type: image/png');
	    echo $img;
	}
	else http_response_code(404);
    }
    else http_response_code(400);
?>

