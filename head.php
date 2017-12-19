<!DOCTYPE html> 
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="application-name" content="TVHadmin" />
<title>TVHadmin - <?php echo $page_title; ?></title>
<link rel="stylesheet" type="text/css" href="style.css" />
</head> 
<body>
<div id="container">
  <div id="navigation">
    <div class="logo">
      <img src="images/logo.png" alt="TVHeadend Logo" width="150" border="0" />
    </div>
    <div class="nav_bar">
<?php
  include_once './include.php';
  foreach ($pages as $key=>$value) {
    echo "<div class='navi'><a href='TVHadmin.php?screen=$key'>$value</a></div>";
  }
?>
      <form action="search.php" method="GET" name="telly" class="search">
        <input type="text" name="find" /><br />
        <input type="submit" name="submit" value="Search" />
      </form>
    </div>
  </div>
