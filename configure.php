<?php
	include_once "include.php";
	$vars = $_POST;
	if (!empty($vars['PROFILE']) && empty($vars['UUID'])) {
	  if($vars['PROFILE'] == '(default)') $vars['PROFILE'] = '';
	  $profiles = get_profiles();
	  foreach($profiles as $p) {
	    if ($p['name'] == $vars['PROFILE']) {
	      $vars['UUID'] = $p['uuid'];
	      break;
	    }
	  }
	}
	$enc = json_encode($vars);
	file_put_contents($config_file, $enc);
	header('Location: fav.php'); 
?>
